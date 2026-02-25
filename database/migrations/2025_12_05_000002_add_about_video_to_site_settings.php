<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert new setting for about_video
        DB::table('site_settings')->insertOrIgnore([
            ['key' => 'about_video', 'value' => null, 'type' => 'video'],
        ]);
    }

    public function down(): void
    {
        // Remove the setting
        DB::table('site_settings')->where('key', 'about_video')->delete();
    }
};

