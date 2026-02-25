<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\UnitActivityLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'units:check-expired-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for units that have been reserved for more than 4 days and convert them to pending status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired reservations...');

        // Find units that are reserved and have been reserved for more than 4 days
        $expiredUnits = Unit::where('status', 'reserved')
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '<=', Carbon::now()->subDays(4))
            ->get();

        if ($expiredUnits->isEmpty()) {
            $this->info('No expired reservations found.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($expiredUnits as $unit) {
            $oldStatus = $unit->status;
            
            $unit->update([
                'status' => 'pending',
                'pending_expires_at' => null, // Clear pending expiration as it's now pending
            ]);

            // Log activity
            UnitActivityLog::create([
                'unit_id' => $unit->id,
                'user_id' => null, // System action
                'action' => 'status_changed',
                'description' => "Reservation expired after 4 days. Status automatically changed from {$oldStatus} to pending",
                'old_data' => ['status' => $oldStatus, 'reserved_at' => $unit->reserved_at],
                'new_data' => ['status' => 'pending'],
            ]);

            $count++;
            $this->line("Unit {$unit->code} (ID: {$unit->id}) - Status changed to pending");
        }

        $this->info("Successfully converted {$count} expired reservation(s) to pending status.");
        return Command::SUCCESS;
    }
}

