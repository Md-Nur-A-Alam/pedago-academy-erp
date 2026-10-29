<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'batch_size',
        'status',
        'assigned_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'batch_size' => 'integer',
            'assigned_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LeadBatchItem::class, 'batch_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }
}
