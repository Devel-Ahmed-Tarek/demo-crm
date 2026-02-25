<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Team;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected ?array $leaderTeamIdsCache = null;

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
            'is_active' => 'boolean',
        ];
    }

    public function assignedLeads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function assignedCustomers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Customer::class, 'assigned_to');
    }

    public function communications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerCommunication::class);
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('membership_type')
            ->withTimestamps();
    }

    public function leadingTeams(): BelongsToMany
    {
        return $this->teams()->wherePivot('membership_type', 'leader');
    }

    public function leaderTeamIds(): array
    {
        if ($this->leaderTeamIdsCache !== null) {
            return $this->leaderTeamIdsCache;
        }

        if ($this->relationLoaded('teams')) {
            $this->leaderTeamIdsCache = $this->teams
                ->where('pivot.membership_type', 'leader')
                ->pluck('id')
                ->all();

            return $this->leaderTeamIdsCache;
        }

        $this->leaderTeamIdsCache = $this->teams()
            ->wherePivot('membership_type', 'leader')
            ->pluck('teams.id')
            ->all();

        return $this->leaderTeamIdsCache;
    }

    public function getPrimaryTeamAttribute(): ?Team
    {
        if ($this->relationLoaded('teams')) {
            return $this->teams->first();
        }

        return $this->teams()->first();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSalesSupervisor(): bool
    {
        return $this->role === 'sales_supervisor';
    }

    public function isSalesAgent(): bool
    {
        return $this->role === 'sales_agent';
    }

    public function isUnitsManager(): bool
    {
        return $this->role === 'units_manager';
    }
}
