<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'office_id',
        'name',
        'email',
        'password',
        'phone',
        'secondary_contact_number',
        'emergency_contact_number',
        'dob',
        'joining_date',
        'nid_number',
        'blood_group',
        'present_address',
        'permanent_address',
        'role',
        'status',
        'edited_by',
        'edited_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'joining_date' => 'date',
            'edited_at' => 'datetime',
        ];
    }

    /**
     * User's shift and daily availability record.
     */
    public function shift(): HasOne
    {
        return $this->hasOne(UserShift::class);
    }

    /**
     * Teams led by this user (if Team Leader).
     */
    public function ledTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'team_leader_id');
    }

    /**
     * Teams supervised by this user (if Supervisor).
     */
    public function supervisedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'supervisor_teams', 'supervisor_id', 'team_id');
    }

    /**
     * Leads actively assigned to this sales executive.
     */
    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'current_owner_id');
    }

    /**
     * Batches allocated to this sales executive.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(LeadBatch::class);
    }

    /**
     * Feedback activities logged by this user.
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(LeadFeedback::class);
    }

    /**
     * Follow-up appointments assigned to this user.
     */
    public function followups(): HasMany
    {
        return $this->hasMany(LeadFollowup::class);
    }

    /**
     * Enrollments converted by this executive.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'sales_executive_id');
    }

    /**
     * User who last edited this record.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(self::class, 'edited_by');
    }

    /**
     * Helper methods for role checking.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'Supervisor';
    }

    public function isTeamLeader(): bool
    {
        return $this->role === 'Team Leader';
    }

    public function isSalesExecutive(): bool
    {
        return $this->role === 'Sales Executive';
    }

    public function isAccounts(): bool
    {
        return $this->role === 'Accounts';
    }
}
