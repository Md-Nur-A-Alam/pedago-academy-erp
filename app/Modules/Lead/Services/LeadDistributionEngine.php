<?php

namespace App\Modules\Lead\Services;

use App\Models\Lead;
use App\Models\LeadBatch;
use App\Models\LeadBatchItem;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadDistributionEngine
{
    /**
     * Run the automated batch distribution engine cycle.
     *
     * @return array<string, mixed>
     */
    public function run(): array
    {
        // 1. Check if the master automated distribution engine toggle is active
        $isEngineActive = SystemSetting::get('lead_distribution_engine_active', true);
        if (! $isEngineActive) {
            return [
                'status' => 'paused',
                'message' => 'Lead distribution engine is currently paused by administrator.',
                'leads_distributed' => 0,
                'executives_assigned' => 0,
            ];
        }

        return DB::transaction(function () {
            // 2. Identify available sales executives eligible for a new batch
            $availableExecutives = $this->getEligibleAvailableExecutives();
            $executiveCount = $availableExecutives->count();

            if ($executiveCount === 0) {
                return [
                    'status' => 'idle',
                    'message' => 'No eligible sales executives available at this moment.',
                    'leads_distributed' => 0,
                    'executives_assigned' => 0,
                ];
            }

            // 3. Lock and fetch unassigned leads in the central queue
            $unassignedLeads = Lead::where('status', 'New')
                ->whereNull('current_owner_id')
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->get();

            $totalLeads = $unassignedLeads->count();

            if ($totalLeads === 0) {
                return [
                    'status' => 'idle',
                    'message' => 'No unassigned leads in queue.',
                    'leads_distributed' => 0,
                    'executives_assigned' => 0,
                ];
            }

            // 4. Calculate dynamic scarcity allocations
            $plan = $this->calculateAllocationPlan($totalLeads, $executiveCount);

            // 5. Execute distribution based on the plan
            $leadsDistributed = 0;
            $executivesAssigned = 0;
            $leadIndex = 0;

            foreach ($availableExecutives as $index => $executive) {
                $assignedCount = $plan[$index] ?? 0;
                if ($assignedCount <= 0) {
                    continue;
                }

                $leadsToAssign = $unassignedLeads->slice($leadIndex, $assignedCount);
                if ($leadsToAssign->isEmpty()) {
                    break;
                }

                $this->assignBatchToExecutive($executive, $leadsToAssign);

                $leadIndex += $assignedCount;
                $leadsDistributed += $leadsToAssign->count();
                $executivesAssigned++;
            }

            return [
                'status' => 'success',
                'message' => "Successfully distributed {$leadsDistributed} leads to {$executivesAssigned} executives.",
                'leads_distributed' => $leadsDistributed,
                'executives_assigned' => $executivesAssigned,
                'remaining_queue_leads' => $totalLeads - $leadsDistributed,
            ];
        });
    }

    /**
     * Fetch all on-duty sales executives eligible to receive new leads.
     *
     * @return Collection<int, User>
     */
    public function getEligibleAvailableExecutives(): Collection
    {
        $cutoffMinutes = (int) SystemSetting::get('shift_cutoff_minutes', 30);
        $lazyThresholdHours = (int) SystemSetting::get('overdue_followup_lazy_threshold_t', 24);
        $currentTime = Carbon::now();

        return User::where('role', 'Sales Executive')
            ->where('status', 'Active')
            ->whereHas('shift', function ($query) {
                $query->where('is_on_duty_today', true)
                    ->where('availability_status', 'Available');
            })
            ->with(['shift', 'ledTeams', 'followups'])
            ->get()
            ->filter(function (User $user) use ($currentTime, $cutoffMinutes, $lazyThresholdHours) {
                $shift = $user->shift;
                if (! $shift) {
                    return false;
                }

                // Check shift start and 30-minute pre-end cutoff window
                $dateStr = $currentTime->toDateString();
                $shiftStart = Carbon::parse("{$dateStr} {$shift->shift_start_time}", $currentTime->timezone);
                $shiftEnd = Carbon::parse("{$dateStr} {$shift->shift_end_time}", $currentTime->timezone);
                $cutoffTime = $shiftEnd->copy()->subMinutes($cutoffMinutes);

                // Handle overnight shifts if applicable
                $isWithinWindow = $shiftEnd->greaterThan($shiftStart)
                    ? $currentTime->between($shiftStart, $cutoffTime)
                    : ($currentTime->gte($shiftStart) || $currentTime->lte($cutoffTime));

                if (! $isWithinWindow) {
                    return false;
                }

                // Check for unresolved overdue follow-ups past threshold t
                $hasOverdueFollowups = $user->followups()
                    ->where('is_done', false)
                    ->where('followup_date_time', '<', $currentTime->copy()->subHours($lazyThresholdHours))
                    ->exists();

                if ($hasOverdueFollowups) {
                    // Automatically penalize with 'Lazy' status
                    $shift->update(['availability_status' => 'Lazy']);
                    Log::warning("Sales Executive [{$user->id}] marked as Lazy due to overdue follow-up timeout.");

                    return false;
                }

                // Ensure the executive has 0 incomplete items in any active batches
                $hasActiveIncompleteItems = LeadBatchItem::whereHas('batch', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->where('status', 'Active');
                })->where('is_completed', false)->exists();

                if ($hasActiveIncompleteItems) {
                    $shift->update(['availability_status' => 'Busy']);

                    return false;
                }

                return true;
            })
            ->values();
    }

    /**
     * Compute distribution counts per agent based on scarcity degradation rules:
     * - Case 1 ($L >= 3*S): n = 3 per agent.
     * - Case 2 (2*S <= $L < 3*S): base n = 2 per agent + remainder 1-by-1 Round Robin.
     * - Case 3 ($L < 2*S): 1-by-1 Round Robin until leads are exhausted.
     *
     * @return array<int, int> Map of agent index => count of leads to assign
     */
    public function calculateAllocationPlan(int $totalLeads, int $executiveCount): array
    {
        $plan = array_fill(0, $executiveCount, 0);

        if ($totalLeads <= 0 || $executiveCount <= 0) {
            return $plan;
        }

        $thresholdStandard = 3 * $executiveCount;
        $thresholdModerate = 2 * $executiveCount;

        if ($totalLeads >= $thresholdStandard) {
            // Case 1: Plentiful Supply (Default n = 3)
            return array_fill(0, $executiveCount, 3);
        }

        if ($totalLeads >= $thresholdModerate) {
            // Case 2: Moderate Scarcity
            // Step 1: Base batch of 2 leads to everyone
            for ($i = 0; $i < $executiveCount; $i++) {
                $plan[$i] = 2;
            }

            // Step 2: Remainder distributed 1-by-1 Round Robin
            $remainder = $totalLeads - (2 * $executiveCount);
            for ($i = 0; $i < $remainder; $i++) {
                $plan[$i] += 1;
            }

            return $plan;
        }

        // Case 3: Severe Scarcity (Single man single lead Round Robin)
        for ($i = 0; $i < $totalLeads && $i < $executiveCount; $i++) {
            $plan[$i] = 1;
        }

        return $plan;
    }

    /**
     * Assign a specific set of leads to an executive in an atomic batch.
     *
     * @param  Collection<int, Lead>  $leads
     */
    protected function assignBatchToExecutive(User $executive, Collection $leads): LeadBatch
    {
        $batch = LeadBatch::create([
            'user_id' => $executive->id,
            'batch_size' => $leads->count(),
            'status' => 'Active',
            'assigned_at' => Carbon::now(),
        ]);

        $teamId = $executive->ledTeams()->value('id');

        foreach ($leads as $lead) {
            // Create batch junction item
            LeadBatchItem::create([
                'batch_id' => $batch->id,
                'lead_id' => $lead->id,
                'is_completed' => false,
            ]);

            // Update lead ownership and state
            $lead->update([
                'current_owner_id' => $executive->id,
                'team_id' => $teamId,
                'status' => 'Assigned',
            ]);
        }

        // Transition executive shift status to Busy
        $executive->shift?->update(['availability_status' => 'Busy']);

        return $batch;
    }
}
