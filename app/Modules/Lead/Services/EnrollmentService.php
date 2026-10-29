<?php

namespace App\Modules\Lead\Services;

use App\Models\Enrollment;
use App\Models\InstallmentSchedule;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class EnrollmentService
{
    /**
     * Generate student enrollment form and secure cryptographic verification link.
     *
     * @param  array<string, mixed>  $data
     * @return array{
     *     enrollment: Enrollment,
     *     verification_url: string,
     *     secure_uuid: string
     * }
     */
    public function createEnrollment(Lead $lead, User $salesExecutive, array $data): array
    {
        return DB::transaction(function () use ($lead, $salesExecutive, $data) {
            $baseFee = (float) ($data['base_course_fee'] ?? $lead->course?->base_fee ?? 0);
            $discountPct = (float) ($data['discount_percentage'] ?? 0);
            $dealAmount = $baseFee - ($baseFee * ($discountPct / 100));
            $installmentsCount = (int) ($data['total_installments'] ?? 1);

            $secureUuid = Str::uuid()->toString();

            $enrollment = Enrollment::create([
                'lead_id' => $lead->id,
                'course_id' => $data['course_id'] ?? $lead->interested_course_id,
                'sales_executive_id' => $salesExecutive->id,
                'secure_uuid' => $secureUuid,
                'student_name' => $data['student_name'] ?? $lead->student_name,
                'student_phone' => $data['student_phone'] ?? $lead->contact_number,
                'student_email' => $data['student_email'] ?? $lead->email,
                'guardian_name' => $data['guardian_name'] ?? null,
                'guardian_phone' => $data['guardian_phone'] ?? $lead->alternative_contact_number,
                'base_course_fee' => $baseFee,
                'discount_percentage' => $discountPct,
                'final_deal_amount' => $dealAmount,
                'total_installments' => $installmentsCount,
                'status' => 'Pending Payment',
            ]);

            // Create installment schedules
            $installmentsData = $data['installments'] ?? [];

            if (! empty($installmentsData)) {
                foreach ($installmentsData as $index => $item) {
                    InstallmentSchedule::create([
                        'enrollment_id' => $enrollment->id,
                        'installment_number' => $index + 1,
                        'due_amount' => $item['due_amount'],
                        'due_date' => $item['due_date'],
                        'status' => 'Pending',
                    ]);
                }
            } else {
                // Default single installment if none specified
                InstallmentSchedule::create([
                    'enrollment_id' => $enrollment->id,
                    'installment_number' => 1,
                    'due_amount' => $dealAmount,
                    'due_date' => Carbon::now()->addDays(3)->toDateString(),
                    'status' => 'Pending',
                ]);
            }

            // Update lead status to Enrolled - Pending Payment
            $lead->update([
                'status' => 'Enrolled - Pending Payment',
            ]);

            return [
                'enrollment' => $enrollment,
                'verification_url' => url("/enroll/verify/{$secureUuid}"),
                'secure_uuid' => $secureUuid,
            ];
        });
    }

    /**
     * Submit payment from customer portal (Gateway IPN or Manual slip submission).
     *
     * @param  array<string, mixed>  $paymentData
     */
    public function submitPayment(Enrollment $enrollment, array $paymentData): Payment
    {
        return DB::transaction(function () use ($enrollment, $paymentData) {
            $trxId = trim($paymentData['transaction_id'] ?? '');
            $slipPath = trim($paymentData['slip_screenshot_path'] ?? '');

            // Rule: Must provide at least one (TrxID OR slip screenshot)
            if (empty($trxId) && empty($slipPath)) {
                throw new InvalidArgumentException('Please provide a valid Transaction ID or upload a deposit receipt screenshot.');
            }

            $amount = (float) $paymentData['amount'];
            $scheduleId = $paymentData['installment_schedule_id'] ?? null;

            if (! $scheduleId) {
                // Default to first pending installment
                $scheduleId = $enrollment->installmentSchedules()
                    ->where('status', 'Pending')
                    ->orderBy('installment_number', 'asc')
                    ->value('id');
            }

            $payment = Payment::create([
                'enrollment_id' => $enrollment->id,
                'installment_schedule_id' => $scheduleId,
                'amount' => $amount,
                'payment_method' => $paymentData['payment_method'],
                'transaction_id' => $trxId ?: null,
                'slip_screenshot_path' => $slipPath ?: null,
                'status' => 'Pending',
            ]);

            // Update installment schedule state
            if ($scheduleId) {
                InstallmentSchedule::where('id', $scheduleId)->update(['status' => 'Under Review']);
            }

            // Update enrollment and lead status to Payment Under Review
            $enrollment->update(['status' => 'Payment Under Review']);
            $enrollment->lead->update(['status' => 'Payment Under Review']);

            return $payment;
        });
    }
}
