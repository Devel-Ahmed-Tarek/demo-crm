<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable(); // emoji or icon name
            $table->string('icon_type')->default('emoji'); // emoji, image
            $table->string('icon_image')->nullable(); // if icon_type is image
            $table->string('link')->nullable(); // CTA link
            $table->string('link_text')->nullable(); // CTA text
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
