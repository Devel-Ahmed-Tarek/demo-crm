<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update status enum to remove 'sold'
        if (Schema::hasColumn('apartments', 'status')) {
            if (DB::getDriverName() === 'mysql') {
                // First, update any existing 'sold' records to 'available'
                DB::statement("UPDATE apartments SET status = 'available' WHERE status = 'sold'");

                // Then update the enum
                DB::statement("ALTER TABLE apartments MODIFY COLUMN status ENUM('available', 'reserved', 'contracted', 'owner') DEFAULT 'available'");
            }
        }
    }

    public function down(): void
    {
        // Restore 'sold' to enum
        if (Schema::hasColumn('apartments', 'status')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE apartments MODIFY COLUMN status ENUM('available', 'reserved', 'sold', 'contracted', 'owner') DEFAULT 'available'");
            }
        }
    }
};
