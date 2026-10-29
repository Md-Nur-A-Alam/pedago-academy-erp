<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuplicateLeadReview extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'incoming_lead_data',
        'matched_lead_id',
        'match_reason',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'incoming_lead_data' => 'array',
            'reviewed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function matchedLead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'matched_lead_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
