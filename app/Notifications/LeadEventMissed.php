<?php

namespace App\Notifications;

use App\Models\LeadActivity;
use Illuminate\Notifications\Notification;

class LeadEventMissed extends Notification
{
    protected $activity;

    /**
     * Create a new notification instance.
     */
    public function __construct(LeadActivity $activity)
    {
        $this->activity = $activity;
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
            'type' => 'lead_event_missed',
            'activity_id' => $this->activity->id,
            'lead_id' => $this->activity->lead_id,
            'message' => __('You missed a scheduled event'),
            'event_title' => $this->activity->title,
            'scheduled_at' => $this->activity->scheduled_at->format('Y-m-d H:i'),
            'lead_name' => $this->activity->lead->name ?? __('Unknown Lead'),
            'url' => route('leads.show', $this->activity->lead_id),
        ];
    }
}
