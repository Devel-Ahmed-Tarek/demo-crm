<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('location');
            $table->decimal('area', 8, 2);
            $table->integer('rooms')->default(0);
            $table->decimal('price', 10, 2);
            $table->enum('status', ['available', 'reserved', 'sold'])->default('available');
            $table->text('description')->nullable();
            $table->foreignId('reserved_by')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('sold_to')->nullable()->constrained('customers')->onDelete('set null');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
