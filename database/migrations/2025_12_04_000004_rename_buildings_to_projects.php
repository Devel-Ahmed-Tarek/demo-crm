<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key constraint first
        Schema::table('building_images', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
        });
        
        Schema::rename('buildings', 'projects');
        Schema::rename('building_images', 'project_images');
        
        // Rename column using raw SQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE project_images CHANGE building_id project_id BIGINT UNSIGNED NOT NULL');
        } else {
            Schema::table('project_images', function (Blueprint $table) {
                $table->renameColumn('building_id', 'project_id');
            });
        }
        
        // Re-add foreign key constraint
        Schema::table('project_images', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Drop foreign key constraint first
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });
        
        // Rename column back
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE project_images CHANGE project_id building_id BIGINT UNSIGNED NOT NULL');
        } else {
            Schema::table('project_images', function (Blueprint $table) {
                $table->renameColumn('project_id', 'building_id');
            });
        }
        
        Schema::rename('project_images', 'building_images');
        Schema::rename('projects', 'buildings');
        
        // Re-add foreign key constraint
        Schema::table('building_images', function (Blueprint $table) {
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
        });
    }
};

