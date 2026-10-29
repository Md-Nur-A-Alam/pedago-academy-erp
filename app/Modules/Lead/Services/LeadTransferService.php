<?php

namespace App\Modules\Lead\Services;

use App\Models\Lead;
use App\Models\LeadTransfer;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeadTransferService
{
    /**
     * Request or execute a lead transfer.
     */
    public function requestTransfer(
        Lead $lead,
        User $sender,
        User $receiver,
        string $reason,
        ?User $actor = null
    ): LeadTransfer {
        return DB::transaction(function () use ($lead, $sender, $receiver, $reason, $actor) {
            $actor = $actor ?? $sender;
            $isAdmin = $actor->isAdmin();

            // Determine sender and receiver teams
            $fromTeamId = $lead->team_id ?? $sender->ledTeams()->value('id') ?? 1;
            $toTeamId = $receiver->ledTeams()->value('id') ?? $fromTeamId;

            // Admin Override Rule: No team leader approval required! Immediate execution.
            if ($isAdmin) {
                $transfer = LeadTransfer::create([
                    'lead_id' => $lead->id,
                    'from_user_id' => $sender->id,
                    'to_user_id' => $receiver->id,
                    'from_team_id' => $fromTeamId,
                    'to_team_id' => $toTeamId,
                    'reason' => $reason.' (Direct Admin Transfer)',
                    'is_admin_override' => true,
                    'status' => 'Approved',
                    'transferred_at' => Carbon::now(),
                ]);

                // Immediately transfer ownership
                $lead->update([
                    'current_owner_id' => $receiver->id,
                    'team_id' => $toTeamId,
                ]);

                return $transfer;
            }

            // Normal Request: Stays Pending without freezing the lead
            return LeadTransfer::create([
                'lead_id' => $lead->id,
                'from_user_id' => $sender->id,
                'to_user_id' => $receiver->id,
                'from_team_id' => $fromTeamId,
                'to_team_id' => $toTeamId,
                'reason' => $reason,
                'is_admin_override' => false,
                'status' => 'Pending',
            ]);
        });
    }

    /**
     * Approve a pending transfer by a Team Leader (Sender TL or Receiver TL).
     */
    public function approve(LeadTransfer $transfer, User $teamLeader): LeadTransfer
    {
        return DB::transaction(function () use ($transfer, $teamLeader) {
            if ($transfer->status !== 'Pending') {
                return $transfer;
            }

            $fromTeam = $transfer->fromTeam;
            $toTeam = $transfer->toTeam;

            // Check if team leader is Sender TL
            if ($fromTeam && (int) $fromTeam->team_leader_id === (int) $teamLeader->id) {
                $transfer->sender_tl_user_id = $teamLeader->id;
                $transfer->sender_tl_approved_at = Carbon::now();
            }

            // Check if team leader is Receiver TL
            if ($toTeam && (int) $toTeam->team_leader_id === (int) $teamLeader->id) {
                $transfer->receiver_tl_user_id = $teamLeader->id;
                $transfer->receiver_tl_approved_at = Carbon::now();
            }

            // If same team or both TLs have approved, complete transfer
            $isSameTeam = $transfer->from_team_id === $transfer->to_team_id;
            $hasBothApprovals = ($transfer->sender_tl_approved_at && $transfer->receiver_tl_approved_at) || $isSameTeam;

            if ($hasBothApprovals) {
                $transfer->status = 'Approved';
                $transfer->transferred_at = Carbon::now();

                // Reassign lead ownership to the new owner
                $transfer->lead->update([
                    'current_owner_id' => $transfer->to_user_id,
                    'team_id' => $transfer->to_team_id,
                ]);
            }

            $transfer->save();

            return $transfer;
        });
    }

    /**
     * Reject a pending transfer.
     */
    public function reject(LeadTransfer $transfer, User $teamLeader, string $rejectionReason): LeadTransfer
    {
        $transfer->update([
            'status' => 'Rejected',
            'reason' => $transfer->reason." | Rejected by TL [{$teamLeader->name}]: {$rejectionReason}",
        ]);

        return $transfer;
    }

    /**
     * 7-Day Auto-Transfer Cron Job Fallback.
     * Automatically executes transfers pending for 7+ days.
     */
    public function processAutomatedTransferFallbacks(): int
    {
        $days = (int) SystemSetting::get('transfer_auto_approval_days', 7);
        $thresholdDate = Carbon::now()->subDays($days);

        $expiredTransfers = LeadTransfer::where('status', 'Pending')
            ->where('created_at', '<=', $thresholdDate)
            ->get();

        $count = 0;
        foreach ($expiredTransfers as $transfer) {
            DB::transaction(function () use ($transfer) {
                $transfer->update([
                    'status' => 'Approved',
                    'is_auto_transferred' => true,
                    'transferred_at' => Carbon::now(),
                ]);

                $transfer->lead->update([
                    'current_owner_id' => $transfer->to_user_id,
                    'team_id' => $transfer->to_team_id,
                ]);
            });
            $count++;
        }

        return $count;
    }
}
