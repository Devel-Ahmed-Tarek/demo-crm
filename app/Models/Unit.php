<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'code',
        'location',
        'area',
        'rooms',
        'price',
        'status',
        'description',
        'reserved_by',
        'sold_to',
        'reserved_at',
        'sold_at',
        'contracted_at',
        'pending_expires_at',
        'sales_comment',
        'floor',
        'column',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'price' => 'decimal:2',
        'reserved_at' => 'datetime',
        'sold_at' => 'datetime',
        'contracted_at' => 'datetime',
        'pending_expires_at' => 'datetime',
    ];

    public function reservedBy(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'reserved_by');
    }

    public function soldTo(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'sold_to');
    }

    public function images(): HasMany
    {
        return $this->hasMany(UnitImage::class)->orderBy('order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(UnitImage::class)->where('is_primary', true);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(UnitFeature::class, 'unit_feature_pivot');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(UnitActivityLog::class)->latest();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class)->orderBy('code');
    }

    /**
     * Check if unit reservation has expired (4 days passed)
     */
    public function isReservationExpired(): bool
    {
        if ($this->status !== 'reserved' || !$this->reserved_at) {
            return false;
        }

        return $this->reserved_at->addDays(4)->isPast();
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => 'white',
            'reserved' => 'yellow',
            'sold' => 'green',
            'pending' => 'orange',
            'contracted' => 'red',
            'owner' => 'blue',
            default => 'gray',
        };
    }
}
