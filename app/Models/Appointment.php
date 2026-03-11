<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'unit_id',
        'user_id',
        'appointment_date',
        'price',
        'notes',
        'status',
        'whatsapp_reminder_sent_at',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'price' => 'decimal:2',
        'whatsapp_reminder_sent_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
