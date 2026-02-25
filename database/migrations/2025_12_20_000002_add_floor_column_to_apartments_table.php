<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            if (!Schema::hasColumn('apartments', 'floor')) {
                $table->integer('floor')->nullable()->after('unit_id');
            }
            if (!Schema::hasColumn('apartments', 'column')) {
                $table->integer('column')->nullable()->after('floor');
            }
            
            // Update status enum if needed
            if (Schema::hasColumn('apartments', 'status')) {
                // For MySQL
                if (DB::getDriverName() === 'mysql') {
                    DB::statement("ALTER TABLE apartments MODIFY COLUMN status ENUM('available', 'reserved', 'sold', 'contracted', 'owner') DEFAULT 'available'");
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            if (Schema::hasColumn('apartments', 'floor')) {
                $table->dropColumn('floor');
            }
            if (Schema::hasColumn('apartments', 'column')) {
                $table->dropColumn('column');
            }
        });
    }
};

