<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\LeadActivity;
use App\Models\CustomerCommunication;
use App\Models\Lead;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\LeadEventMissed;

class CalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = now();
        $fifteenMinutesAgo = $now->copy()->subMinutes(15);

        // Get missed events count for button badge
        $missedEventsCount = $this->getMissedEventsQuery($user, $now, $fifteenMinutesAgo)->count();

        return view('calendar.index', compact('missedEventsCount'));
    }

    public function missedEvents()
    {
        $user = Auth::user();
        $now = now();
        $fifteenMinutesAgo = $now->copy()->subMinutes(15);

        // Get missed events (events that passed 15 minutes or more, but within last 7 days)
        $missedEvents = $this->getMissedEventsQuery($user, $now, $fifteenMinutesAgo)
            ->orderBy('scheduled_at', 'desc')
            ->paginate(20);

        return view('calendar.missed-events', compact('missedEvents'));
    }

    protected function getMissedEventsQuery($user, $now, $fifteenMinutesAgo)
    {
        $missedEventsQuery = LeadActivity::with(['lead', 'user'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<', $fifteenMinutesAgo)
            ->where('scheduled_at', '>', $now->copy()->subDays(7));

        // Apply user scope
        if ($user->isSalesAgent()) {
            $missedEventsQuery->whereHas('lead', function ($leadQuery) use ($user) {
                $leadQuery->where('assigned_to', $user->id);
            });
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $missedEventsQuery->whereHas('lead', function ($leadQuery) use ($teamIds) {
                    $leadQuery->whereIn('team_id', $teamIds);
                });
            } else {
                $missedEventsQuery->whereRaw('1 = 0');
            }
        }

        return $missedEventsQuery;
    }

    public function events(Request $request)
    {
        $user = Auth::user();
        $start = $request->input('start');
        $end = $request->input('end');

        $events = collect();

        // Appointments
        $appointmentsQuery = Appointment::with(['customer', 'unit', 'user'])
            ->whereBetween('appointment_date', [$start, $end]);

        if ($user->isSalesAgent()) {
            $appointmentsQuery->where('user_id', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $appointmentsQuery->whereHas('customer', function ($q) use ($teamIds) {
                    $q->whereIn('team_id', $teamIds);
                });
            } else {
                $appointmentsQuery->whereRaw('1 = 0');
            }
        }

        $appointments = $appointmentsQuery->get();
        foreach ($appointments as $appointment) {
            $events->push([
                'id' => 'appointment_' . $appointment->id,
                'title' => ($appointment->customer->name ?? __('Unknown')),
                'start' => $appointment->appointment_date->toIso8601String(),
                'end' => $appointment->appointment_date->copy()->addHour()->toIso8601String(),
                'color' => '#ec4899', // pink
                'classNames' => ['event-appointment'],
                'type' => 'appointment',
                'resourceId' => $appointment->id,
                'extendedProps' => [
                    'status' => $appointment->status,
                    'customer' => $appointment->customer->name ?? null,
                    'unit' => $appointment->unit->code ?? null,
                    'user' => $appointment->user->name ?? null,
                    'notes' => $appointment->notes,
                    'canEdit' => $this->canEditAppointment($user, $appointment),
                ],
            ]);
        }

        // Lead Activities (scheduled)
        $activitiesQuery = LeadActivity::with(['lead', 'user'])
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$start, $end]);

        if ($user->isSalesAgent()) {
            $activitiesQuery->where('user_id', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $activitiesQuery->whereHas('lead', function ($q) use ($teamIds) {
                    $q->whereIn('team_id', $teamIds);
                });
            } else {
                $activitiesQuery->whereRaw('1 = 0');
            }
        }

        $activities = $activitiesQuery->get();
        foreach ($activities as $activity) {
            $events->push([
                'id' => 'activity_' . $activity->id,
                'title' => $activity->title,
                'start' => $activity->scheduled_at->toIso8601String(),
                'end' => $activity->scheduled_at->copy()->addHour()->toIso8601String(),
                'color' => '#8b5cf6', // purple
                'classNames' => ['event-activity'],
                'type' => 'activity',
                'resourceId' => $activity->id,
                'extendedProps' => [
                    'activity_type' => $activity->activity_type,
                    'lead' => $activity->lead->name ?? null,
                    'lead_id' => $activity->lead_id,
                    'user' => $activity->user->name ?? null,
                    'details' => $activity->details,
                    'canEdit' => $this->canEditActivity($user, $activity),
                ],
            ]);
        }

        // Customer Communications (scheduled)
        $communicationsQuery = CustomerCommunication::with(['customer', 'user'])
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$start, $end]);

        if ($user->isSalesAgent()) {
            $communicationsQuery->where('user_id', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $communicationsQuery->whereHas('customer', function ($q) use ($teamIds) {
                    $q->whereIn('team_id', $teamIds);
                });
            } else {
                $communicationsQuery->whereRaw('1 = 0');
            }
        }

        $communications = $communicationsQuery->get();
        foreach ($communications as $communication) {
            $events->push([
                'id' => 'communication_' . $communication->id,
                'title' => ($communication->customer->name ?? __('Unknown')),
                'start' => $communication->scheduled_at->toIso8601String(),
                'end' => $communication->scheduled_at->copy()->addHour()->toIso8601String(),
                'color' => '#10b981', // green
                'classNames' => ['event-communication'],
                'type' => 'communication',
                'resourceId' => $communication->id,
                'extendedProps' => [
                    'type' => $communication->type,
                    'customer' => $communication->customer->name ?? null,
                    'user' => $communication->user->name ?? null,
                    'notes' => $communication->notes,
                    'completed_at' => $communication->completed_at?->toIso8601String(),
                    'canEdit' => $this->canEditCommunication($user, $communication),
                ],
            ]);
        }

        return response()->json($events->values());
    }

    protected function canEditAppointment($user, Appointment $appointment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isSalesAgent()) {
            return $appointment->user_id === $user->id;
        }

        if ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            return !empty($teamIds) && $appointment->customer && in_array($appointment->customer->team_id, $teamIds);
        }

        return false;
    }

    protected function canEditActivity($user, LeadActivity $activity): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isSalesAgent()) {
            return $activity->user_id === $user->id;
        }

        if ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            return !empty($teamIds) && $activity->lead && in_array($activity->lead->team_id, $teamIds);
        }

        return false;
    }

    protected function canEditCommunication($user, CustomerCommunication $communication): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isSalesAgent()) {
            return $communication->user_id === $user->id;
        }

        if ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            return !empty($teamIds) && $communication->customer && in_array($communication->customer->team_id, $teamIds);
        }

        return false;
    }
}
