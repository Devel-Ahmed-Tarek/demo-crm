<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `leads` MODIFY `stage` VARCHAR(191) NOT NULL DEFAULT 'new'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `leads` MODIFY `stage` ENUM('new','contacted','follow-up','proposal','won','lost') NOT NULL DEFAULT 'new'");
    }
};

