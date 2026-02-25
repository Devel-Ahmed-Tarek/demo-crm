<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification
{

    protected $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'appointment_reminder',
            'appointment_id' => $this->appointment->id,
            'message' => __('You have an appointment in 15 minutes'),
            'appointment_date' => $this->appointment->appointment_date->format('Y-m-d H:i'),
            'customer_name' => $this->appointment->customer->name ?? __('Unknown Customer'),
            'unit_code' => $this->appointment->unit->code ?? null,
            'url' => route('appointments.index'),
        ];
    }
}
