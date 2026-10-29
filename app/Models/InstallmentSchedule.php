<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallmentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'installment_number',
        'due_amount',
        'due_date',
        'followup_generated',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'installment_number' => 'integer',
            'due_amount' => 'decimal:2',
            'due_date' => 'date',
            'followup_generated' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
