<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, json
            $table->timestamps();
        });

        // Insert default settings
        DB::table('site_settings')->insert([
            ['key' => 'hero_image', 'value' => null, 'type' => 'image'],
            ['key' => 'site_name', 'value' => 'WE SOLD', 'type' => 'text'],
            ['key' => 'site_logo', 'value' => null, 'type' => 'image'],
            ['key' => 'site_favicon', 'value' => null, 'type' => 'image'],
            ['key' => 'site_address', 'value' => null, 'type' => 'text'],
            ['key' => 'site_phone', 'value' => null, 'type' => 'text'],
            ['key' => 'site_email', 'value' => null, 'type' => 'text'],
            ['key' => 'site_facebook', 'value' => null, 'type' => 'text'],
            ['key' => 'site_twitter', 'value' => null, 'type' => 'text'],
            ['key' => 'site_instagram', 'value' => null, 'type' => 'text'],
            ['key' => 'site_linkedin', 'value' => null, 'type' => 'text'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
