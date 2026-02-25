<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'primary_team_id')) {
                $table->foreignId('primary_team_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('teams')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'primary_team_id')) {
                $table->dropForeign(['primary_team_id']);
                $table->dropColumn('primary_team_id');
            }
        });
    }
};

