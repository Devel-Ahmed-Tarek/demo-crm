<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\AppointmentReminder;
use App\Notifications\AppointmentMissed;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['customer', 'unit', 'user']);

        $user = $request->user();
        if ($user->isSalesAgent()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('customer', function ($customerQuery) use ($teamIds) {
                    $customerQuery->whereIn('team_id', $teamIds);
                });
            }
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $appointments = $query->latest('appointment_date')->paginate(20);
        $customers = $this->visibleCustomers($user)->get();
        $units = Unit::where('status', 'available')->get();

        return view('appointments.index', compact('appointments', 'customers', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'unit_id' => 'nullable|exists:units,id',
            'appointment_date' => 'required|date',
            'price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'scheduled';

        $appointment = Appointment::create($validated);
        $appointment->load(['customer', 'unit', 'user']);

        // Send notifications immediately
        $this->checkAndSendAppointmentNotifications($appointment);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully');
    }

    public function edit(Appointment $appointment)
    {
        $this->ensureCanAccessAppointment($appointment);
        $appointment->load(['customer', 'unit', 'user']);
        return view('appointments.edit', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $this->ensureCanAccessAppointment($appointment);
        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        $appointment->update($validated);
        $appointment->load(['customer', 'unit', 'user']);

        // Send notifications immediately if status is scheduled
        if ($validated['status'] === 'scheduled') {
            $this->checkAndSendAppointmentNotifications($appointment);
        }

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully');
    }

    public function destroy(Appointment $appointment)
    {
        $this->ensureCanAccessAppointment($appointment);
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Only administrators can perform this action.');
        }

        $ids = collect($request->input('ids', []))->filter()->unique();

        if ($ids->isEmpty()) {
            return redirect()->route('appointments.index')->with('error', 'Please select at least one appointment to delete.');
        }

        Appointment::whereIn('id', $ids)->delete();

        return redirect()->route('appointments.index')->with('success', 'Selected appointments deleted successfully.');
    }

    protected function ensureCanAccessAppointment(Appointment $appointment): void
    {
        $user = auth()->user();

        if ($user && $user->isSalesAgent() && $appointment->user_id !== $user->id) {
            abort(403, 'You are not allowed to access this appointment.');
        }

        if ($user && $user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            $appointment->loadMissing('customer');

            if (empty($teamIds) || !$appointment->customer || !$appointment->customer->team_id || !in_array($appointment->customer->team_id, $teamIds)) {
                abort(403, 'You are not allowed to access this appointment.');
            }
        }
    }

    protected function visibleCustomers($user)
    {
        $query = Customer::query();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isSalesAgent()) {
            return $query->where('assigned_to', $user->id);
        }

        if ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds)) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereIn('team_id', $teamIds);
        }

        return $query;
    }

    /**
     * Check appointment and send notifications immediately
     */
    protected function checkAndSendAppointmentNotifications(Appointment $appointment): void
    {
        if ($appointment->status !== 'scheduled') {
            return;
        }

        $now = now();
        $appointmentTime = $appointment->appointment_date;
        $fifteenMinutesFromNow = $now->copy()->addMinutes(15);
        $oneHourAgo = $now->copy()->subHour();
        $user = $appointment->user;

        if (!$user) {
            return;
        }

        // Check if appointment is missed (past appointment time but within last hour)
        if ($appointmentTime->isPast() && $appointmentTime->isAfter($oneHourAgo)) {
            // Send notification to sales agent
            $user->notify(new AppointmentMissed($appointment));

            // Send notification to admin
            $admins = User::where('role', 'admin')->where('is_active', true)->get();
            foreach ($admins as $admin) {
                $admin->notify(new AppointmentMissed($appointment));
            }

            // Send notification to team leader
            if ($user->isSalesAgent()) {
                $teamIds = $user->teams()->pluck('teams.id')->toArray();
                if (!empty($teamIds)) {
                    $teamLeaders = User::whereHas('teams', function ($query) use ($teamIds) {
                        $query->whereIn('teams.id', $teamIds)
                            ->where('team_user.membership_type', 'leader');
                    })->where('is_active', true)->get();

                    foreach ($teamLeaders as $leader) {
                        $leader->notify(new AppointmentMissed($appointment));
                    }
                }
            }
        }

        // Check if appointment is in 15 minutes
        if ($appointmentTime->isAfter($now) && $appointmentTime->isBefore($fifteenMinutesFromNow)) {
            // Send reminder to sales agent
            $user->notify(new AppointmentReminder($appointment));

            // Send reminder to admin
            $admins = User::where('role', 'admin')->where('is_active', true)->get();
            foreach ($admins as $admin) {
                $admin->notify(new AppointmentReminder($appointment));
            }

            // Send reminder to team leader
            if ($user->isSalesAgent()) {
                $teamIds = $user->teams()->pluck('teams.id')->toArray();
                if (!empty($teamIds)) {
                    $teamLeaders = User::whereHas('teams', function ($query) use ($teamIds) {
                        $query->whereIn('teams.id', $teamIds)
                            ->where('team_user.membership_type', 'leader');
                    })->where('is_active', true)->get();

                    foreach ($teamLeaders as $leader) {
                        $leader->notify(new AppointmentReminder($appointment));
                    }
                }
            }
        }
    }
}
