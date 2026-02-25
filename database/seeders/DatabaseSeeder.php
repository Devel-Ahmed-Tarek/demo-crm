<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin and test users
        $this->call([
            AdminUserSeeder::class,
            LeadStagesSeeder::class,
            LeadSourcesSeeder::class,
            TeamSeeder::class,
        ]);
    }
}
