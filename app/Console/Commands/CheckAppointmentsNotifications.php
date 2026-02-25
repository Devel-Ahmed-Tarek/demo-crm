<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\User;
use App\Notifications\AppointmentMissed;
use App\Notifications\AppointmentReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAppointmentsNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:check-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check appointments and send notifications for missed and upcoming appointments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $fifteenMinutesFromNow = $now->copy()->addMinutes(15);
        $oneHourAgo = $now->copy()->subHour();

        // Get scheduled appointments that are either missed (past) or upcoming (within 15 minutes)
        $appointments = Appointment::with(['user', 'customer', 'unit'])
            ->where('status', 'scheduled')
            ->where(function($query) use ($now, $fifteenMinutesFromNow, $oneHourAgo) {
                // Missed appointments (past but within last hour)
                $query->where(function($q) use ($now, $oneHourAgo) {
                    $q->where('appointment_date', '<', $now)
                      ->where('appointment_date', '>=', $oneHourAgo);
                })
                // Upcoming appointments (within next 15 minutes)
                ->orWhere(function($q) use ($now, $fifteenMinutesFromNow) {
                    $q->where('appointment_date', '>', $now)
                      ->where('appointment_date', '<=', $fifteenMinutesFromNow);
                });
            })
            ->get();

        foreach ($appointments as $appointment) {
            $appointmentTime = $appointment->appointment_date;
            $user = $appointment->user;
            
            if (!$user) {
                continue;
            }

            // Check if appointment is missed (past appointment time)
            if ($appointmentTime->isPast() && $appointmentTime->isAfter($now->copy()->subHour())) {
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

        $this->info('Appointment notifications checked successfully.');
        return 0;
    }
}

