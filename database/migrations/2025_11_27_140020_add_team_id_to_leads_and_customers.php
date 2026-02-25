<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            if (!Schema::hasColumn('leads', 'team_id')) {
                $table->foreignId('team_id')
                    ->nullable()
                    ->after('assigned_to')
                    ->constrained('teams')
                    ->nullOnDelete();
            }
        });

        Schema::table('customers', function (Blueprint $table): void {
            if (!Schema::hasColumn('customers', 'team_id')) {
                $table->foreignId('team_id')
                    ->nullable()
                    ->after('assigned_to')
                    ->constrained('teams')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            if (Schema::hasColumn('leads', 'team_id')) {
                $table->dropConstrainedForeignId('team_id');
            }
        });

        Schema::table('customers', function (Blueprint $table): void {
            if (Schema::hasColumn('customers', 'team_id')) {
                $table->dropConstrainedForeignId('team_id');
            }
        });
    }
};

