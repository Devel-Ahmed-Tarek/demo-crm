<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert new settings for phone2 and address2
        DB::table('site_settings')->insertOrIgnore([
            ['key' => 'site_phone2', 'value' => null, 'type' => 'text'],
            ['key' => 'site_address2', 'value' => null, 'type' => 'text'],
        ]);
    }

    public function down(): void
    {
        // Remove the settings
        DB::table('site_settings')->whereIn('key', ['site_phone2', 'site_address2'])->delete();
    }
};

