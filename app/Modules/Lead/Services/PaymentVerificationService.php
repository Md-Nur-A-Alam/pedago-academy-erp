<?php

namespace App\Modules\Lead\Services;

use App\Models\InstallmentSchedule;
use App\Models\LeadBatchItem;
use App\Models\LeadFollowup;
use App\Models\Payment;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentVerificationService
{
    /**
     * Accounts Department approves payment submission.
     * Reconciles transaction, marks batch task completed, and generates Money Receipt.
     *
     * @return array<string, mixed>
     */
    public function approvePayment(Payment $payment, User $accountsUser): array
    {
        return DB::transaction(function () use ($payment, $accountsUser) {
            $payment->update([
                'status' => 'Verified',
                'verified_by_user_id' => $accountsUser->id,
                'verified_at' => Carbon::now(),
            ]);

            $enrollment = $payment->enrollment;
            $lead = $enrollment->lead;

            // 1. Mark associated installment schedule as Paid
            if ($payment->installment_schedule_id) {
                InstallmentSchedule::where('id', $payment->installment_schedule_id)->update([
                    'status' => 'Paid',
                    'paid_at' => Carbon::now(),
                ]);
            }

            // 2. Complete the Sales Executive's batch task
            $batchItem = LeadBatchItem::where('lead_id', $lead->id)
                ->where('is_completed', false)
                ->latest()
                ->first();

            if ($batchItem) {
                $batchItem->update([
                    'is_completed' => true,
                    'action_taken' => 'Converted & Verified',
                    'completed_at' => Carbon::now(),
                ]);

                // Check if the entire batch is now complete
                $batch = $batchItem->batch;
                $hasIncompleteItems = $batch->items()->where('is_completed', false)->exists();

                if (! $hasIncompleteItems) {
                    $batch->update([
                        'status' => 'Completed',
                        'completed_at' => Carbon::now(),
                    ]);

                    // Check if sales executive is now completely free of active batch tasks
                    $executive = $batch->user;
                    $hasAnyActiveItems = LeadBatchItem::whereHas('batch', function ($q) use ($executive) {
                        $q->where('user_id', $executive->id)->where('status', 'Active');
                    })->where('is_completed', false)->exists();

                    if (! $hasAnyActiveItems && $executive->shift && $executive->shift->availability_status !== 'Lazy') {
                        $executive->shift->update(['availability_status' => 'Available']);
                    }
                }
            }

            // 3. Check overall installment progress
            $unpaidInstallmentsCount = $enrollment->installmentSchedules()
                ->where('status', '!=', 'Paid')
                ->count();

            $enrollment->update(['status' => 'Enrolled']);
            $lead->update(['status' => 'Converted']);

            $isFullyPaid = ($unpaidInstallmentsCount === 0);

            return [
                'status' => 'success',
                'message' => 'Payment successfully verified and approved.',
                'is_fully_paid' => $isFullyPaid,
                'unpaid_installments_remaining' => $unpaidInstallmentsCount,
                'receipt_number' => 'MR-'.date('Ymd').'-'.str_pad((string) $payment->id, 5, '0', STR_PAD_LEFT),
            ];
        });
    }

    /**
     * Accounts Department rejects payment submission (e.g. invalid TrxID or fake slip).
     */
    public function rejectPayment(Payment $payment, User $accountsUser, string $reason): Payment
    {
        return DB::transaction(function () use ($payment, $accountsUser, $reason) {
            $payment->update([
                'status' => 'Rejected',
                'rejection_reason' => $reason,
                'verified_by_user_id' => $accountsUser->id,
                'verified_at' => Carbon::now(),
            ]);

            // Revert installment schedule
            if ($payment->installment_schedule_id) {
                InstallmentSchedule::where('id', $payment->installment_schedule_id)->update([
                    'status' => 'Pending',
                ]);
            }

            // Revert enrollment and lead status to Enrolled - Pending Payment
            $enrollment = $payment->enrollment;
            $enrollment->update(['status' => 'Pending Payment']);
            $enrollment->lead->update(['status' => 'Enrolled - Pending Payment']);

            return $payment;
        });
    }

    /**
     * Automated T-2 Days Installment Follow-up Scheduler.
     * Moves upcoming installments to the sales executive's follow-up queue 2 days before due date.
     */
    public function processInstallmentFollowupAlerts(): int
    {
        $leadDays = (int) SystemSetting::get('installment_followup_lead_days', 2);
        $targetDate = Carbon::now()->addDays($leadDays)->toDateString();

        $dueSchedules = InstallmentSchedule::where('status', 'Pending')
            ->where('followup_generated', false)
            ->whereDate('due_date', '<=', $targetDate)
            ->with(['enrollment.lead'])
            ->get();

        $count = 0;
        foreach ($dueSchedules as $schedule) {
            DB::transaction(function () use ($schedule) {
                $enrollment = $schedule->enrollment;
                $lead = $enrollment->lead;

                // Create follow-up task for the assigned sales executive
                LeadFollowup::create([
                    'lead_id' => $lead->id,
                    'user_id' => $enrollment->sales_executive_id,
                    'followup_date_time' => Carbon::parse($schedule->due_date)->setTime(10, 0),
                    'note' => "Upcoming installment #{$schedule->installment_number} due on {$schedule->due_date}. Amount: ৳".number_format($schedule->due_amount, 2),
                    'is_done' => false,
                ]);

                $schedule->update(['followup_generated' => true]);
            });
            $count++;
        }

        return $count;
    }
}
