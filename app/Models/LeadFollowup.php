<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'followup_date_time',
        'note',
        'is_done',
        'overdue_alert_sent',
    ];

    protected function casts(): array
    {
        return [
            'followup_date_time' => 'datetime',
            'is_done' => 'boolean',
            'overdue_alert_sent' => 'boolean',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOverdue(): bool
    {
        return ! $this->is_done && $this->followup_date_time->isPast();
    }
}
