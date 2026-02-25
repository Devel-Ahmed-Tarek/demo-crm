<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'source',
        'stage',
        'notes',
        'assigned_to',
        'customer_id',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(LeadTag::class, 'lead_tag_pivot');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)
            ->orderByDesc('scheduled_at')
            ->orderByDesc('created_at');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LeadComment::class)->orderByDesc('created_at');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LeadActivity::class)
            ->whereNotNull('scheduled_at')
            ->orderBy('scheduled_at');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
