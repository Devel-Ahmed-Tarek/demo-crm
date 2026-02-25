<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'assigned_to',
        'team_id',
        'last_contacted_at',
        'next_followup_at',
    ];

    protected $casts = [
        'last_contacted_at' => 'datetime',
        'next_followup_at' => 'datetime',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(CustomerCommunication::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function reservedUnits(): HasMany
    {
        return $this->hasMany(Unit::class, 'reserved_by');
    }

    public function purchasedUnits(): HasMany
    {
        return $this->hasMany(Unit::class, 'sold_to');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
