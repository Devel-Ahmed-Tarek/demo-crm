<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DemoDataSeeder;

class SeedDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:seed {--fresh : Drop all tables and re-run migrations first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed demo data for CRM system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            if (!$this->confirm('This will delete all existing data. Are you sure?')) {
                $this->info('Cancelled.');
                return 0;
            }

            $this->info('🔄 Running fresh migrations...');
            $this->call('migrate:fresh');
        }

        $this->info('🌱 Seeding demo data...');
        $this->call('db:seed', ['--class' => DemoDataSeeder::class]);

        $this->info('✅ Demo data seeded successfully!');
        $this->info('');
        $this->info('📝 Login Credentials:');
        $this->info('   Admin: admin@admin.com / password');
        $this->info('   Supervisor: supervisor@test.com / password');
        $this->info('   Agent 1: agent1@test.com / password');
        $this->info('   Agent 2: agent2@test.com / password');
        $this->info('   Units Manager: units@test.com / password');

        return 0;
    }
}