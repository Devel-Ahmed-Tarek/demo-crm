<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->integer('floor')->nullable(); // الطابق
            $table->integer('column')->nullable(); // العمود
            $table->string('code'); // رقم الشقة
            $table->decimal('area', 10, 2)->nullable(); // المساحة
            $table->integer('rooms')->nullable(); // عدد الغرف
            $table->decimal('price', 12, 2)->nullable(); // السعر
            $table->enum('status', ['available', 'reserved', 'sold', 'contracted', 'owner'])->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['unit_id', 'code']); // رقم الشقة فريد داخل الوحدة
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
