<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_start_time',
        'shift_end_time',
        'is_on_duty_today',
        'availability_status',
        'last_active_at',
    ];

    protected function casts(): array
    {
        return [
            'is_on_duty_today' => 'boolean',
            'last_active_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isAvailable(): bool
    {
        return $this->availability_status === 'Available';
    }

    public function isBusy(): bool
    {
        return $this->availability_status === 'Busy';
    }

    public function isLazy(): bool
    {
        return $this->availability_status === 'Lazy';
    }
}
