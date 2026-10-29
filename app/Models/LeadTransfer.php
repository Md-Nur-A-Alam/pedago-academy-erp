<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'from_user_id',
        'to_user_id',
        'from_team_id',
        'to_team_id',
        'reason',
        'sender_tl_user_id',
        'sender_tl_approved_at',
        'receiver_tl_user_id',
        'receiver_tl_approved_at',
        'is_auto_transferred',
        'is_admin_override',
        'status',
        'transferred_at',
    ];

    protected function casts(): array
    {
        return [
            'sender_tl_approved_at' => 'datetime',
            'receiver_tl_approved_at' => 'datetime',
            'is_auto_transferred' => 'boolean',
            'is_admin_override' => 'boolean',
            'transferred_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function fromTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'from_team_id');
    }

    public function toTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'to_team_id');
    }

    public function senderTeamLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_tl_user_id');
    }

    public function receiverTeamLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_tl_user_id');
    }
}
