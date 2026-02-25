<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $defaultTeams = [
            ['name' => 'Alpha Team', 'color' => '#6366F1'],
            ['name' => 'Bravo Squad', 'color' => '#F97316'],
            ['name' => 'Growth Crew', 'color' => '#10B981'],
        ];

        foreach ($defaultTeams as $team) {
            Team::firstOrCreate(
                ['slug' => Str::slug($team['name'])],
                [
                    'name' => $team['name'],
                    'color' => $team['color'],
                    'description' => 'Auto generated team',
                    'is_active' => true,
                ]
            );
        }
    }
}

