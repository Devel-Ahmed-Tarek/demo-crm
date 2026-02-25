<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Notifications\Notification;

class LeadAssigned extends Notification
{

    protected $lead;
    protected $assignedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Lead $lead, $assignedBy = null)
    {
        $this->lead = $lead;
        $this->assignedBy = $assignedBy;
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
        $assignedByName = $this->assignedBy ? $this->assignedBy->name : __('System');

        return [
            'type' => 'lead_assigned',
            'lead_id' => $this->lead->id,
            'message' => __('A new lead has been assigned to you'),
            'lead_name' => $this->lead->name,
            'assigned_by' => $assignedByName,
            'url' => route('leads.show', $this->lead),
        ];
    }
}
