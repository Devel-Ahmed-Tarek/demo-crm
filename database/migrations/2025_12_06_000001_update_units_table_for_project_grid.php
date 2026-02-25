<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // Add project_id foreign key
            $table->foreignId('project_id')->nullable()->after('id')->constrained('projects')->onDelete('cascade');
            
            // Add floor and column for grid positioning
            $table->integer('floor')->nullable()->after('project_id');
            $table->integer('column')->nullable()->after('floor');
            
            // Add contracted_at timestamp
            $table->timestamp('contracted_at')->nullable()->after('sold_at');
            
            // Add pending_expires_at for auto-expiring reservations
            $table->timestamp('pending_expires_at')->nullable()->after('reserved_at');
        });

        // Update status enum to include new statuses
        // Note: MySQL doesn't support ALTER ENUM directly, so we need to use raw SQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('available', 'reserved', 'sold', 'pending', 'contracted', 'owner') DEFAULT 'available'");
        } else {
            // For other databases, we'll need to recreate the column
            Schema::table('units', function (Blueprint $table) {
                $table->enum('status', ['available', 'reserved', 'sold', 'pending', 'contracted', 'owner'])
                      ->default('available')
                      ->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['project_id', 'floor', 'column', 'contracted_at', 'pending_expires_at']);
        });

        // Revert status enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('available', 'reserved', 'sold') DEFAULT 'available'");
        } else {
            Schema::table('units', function (Blueprint $table) {
                $table->enum('status', ['available', 'reserved', 'sold'])
                      ->default('available')
                      ->change();
            });
        }
    }
};

