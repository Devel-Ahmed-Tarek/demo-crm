<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $adminExists = User::where('email', 'admin@admin.com')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]);

            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@admin.com');
            $this->command->info('Password: password');
        } else {
            $this->command->warn('Admin user already exists!');
        }

        // Create test users with different roles
        $testUsers = [
            [
                'name' => 'Sales Supervisor',
                'email' => 'supervisor@test.com',
                'password' => Hash::make('password'),
                'role' => 'sales_supervisor',
                'is_active' => true,
            ],
            [
                'name' => 'Sales Agent',
                'email' => 'agent@test.com',
                'password' => Hash::make('password'),
                'role' => 'sales_agent',
                'is_active' => true,
            ],
            [
                'name' => 'Units Manager',
                'email' => 'units@test.com',
                'password' => Hash::make('password'),
                'role' => 'units_manager',
                'is_active' => true,
            ],
        ];

        foreach ($testUsers as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                User::create($userData);
                $this->command->info("Created user: {$userData['email']}");
            }
        }
    }
}
