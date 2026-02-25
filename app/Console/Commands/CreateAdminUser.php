<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--email=admin@admin.com} {--password=password} {--name=Admin} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser && !$this->option('force')) {
            $this->warn("User with email {$email} already exists!");
            $this->info("Current admin credentials:");
            $this->info("Email: {$email}");
            $this->info("Name: {$existingUser->name}");
            $this->info("Role: {$existingUser->role}");
            $this->info("Status: " . ($existingUser->is_active ? 'Active' : 'Inactive'));
            $this->info("\nTo reset password, use: php artisan admin:create --email={$email} --password=yourpassword --force");
            return 0;
        }

        if ($existingUser && $this->option('force')) {
            // Update existing user
            $existingUser->update([
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
            ]);
            $this->info('Admin user updated successfully!');
            $this->info("Email: {$email}");
            $this->info("Password: {$password} (updated)");
            $this->info("Name: {$name}");
            return 0;
        }

        // Create admin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->info('Admin user created successfully!');
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->info("Name: {$name}");

        return 0;
    }
}
