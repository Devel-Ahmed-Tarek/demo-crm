<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Lead;
use App\Models\LeadTag;
use App\Models\Customer;
use App\Models\Unit;
use App\Models\UnitFeature;
use App\Models\UnitImage;
use App\Models\Appointment;
use App\Models\CustomerCommunication;
use App\Models\UnitActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Demo Data Seeding...');

        // 1. Create/Get Users
        $this->command->info('👥 Creating users...');
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@test.com'],
            [
                'name' => 'Ahmed Supervisor',
                'password' => Hash::make('password'),
                'role' => 'sales_supervisor',
                'is_active' => true,
            ]
        );

        $agent1 = User::firstOrCreate(
            ['email' => 'agent1@test.com'],
            [
                'name' => 'Mohamed Agent',
                'password' => Hash::make('password'),
                'role' => 'sales_agent',
                'is_active' => true,
            ]
        );

        $agent2 = User::firstOrCreate(
            ['email' => 'agent2@test.com'],
            [
                'name' => 'Sara Agent',
                'password' => Hash::make('password'),
                'role' => 'sales_agent',
                'is_active' => true,
            ]
        );

        $unitsManager = User::firstOrCreate(
            ['email' => 'units@test.com'],
            [
                'name' => 'Ali Units Manager',
                'password' => Hash::make('password'),
                'role' => 'units_manager',
                'is_active' => true,
            ]
        );

        $salesAgents = [$agent1, $agent2, $supervisor];

        // 2. Create Lead Tags
        $this->command->info('🏷️ Creating lead tags...');
        $tags = [
            ['name' => 'Hot Lead', 'color' => '#ef4444'],
            ['name' => 'VIP', 'color' => '#f59e0b'],
            ['name' => 'Interested', 'color' => '#10b981'],
            ['name' => 'Follow Up', 'color' => '#3b82f6'],
            ['name' => 'Cold Lead', 'color' => '#6b7280'],
        ];

        $leadTags = [];
        foreach ($tags as $tagData) {
            $leadTags[] = LeadTag::firstOrCreate(
                ['name' => $tagData['name']],
                ['color' => $tagData['color']]
            );
        }

        // 3. Create Unit Features
        $this->command->info('🏠 Creating unit features...');
        $features = [
            'Swimming Pool',
            'Parking',
            'Elevator',
            'Garden',
            'Balcony',
            'Security',
            'Air Conditioning',
            'Furnished',
            'Sea View',
            'Near Beach'
        ];

        $unitFeatures = [];
        foreach ($features as $featureName) {
            $unitFeatures[] = UnitFeature::firstOrCreate(['name' => $featureName]);
        }

        // 4. Create Customers
        $this->command->info('👤 Creating customers...');
        $customers = [];
        $customerNames = [
            'Ahmed Mohamed',
            'Sara Ali',
            'Mohamed Hassan',
            'Fatima Ahmed',
            'Omar Khalid',
            'Aisha Mostafa',
            'Hassan Ibrahim',
            'Nour Youssef',
            'Youssef Adel',
            'Mariam Tarek',
            'Khaled Samir',
            'Dina Wael',
            'Mahmoud Nader',
            'Laila Rami',
            'Wael Hani'
        ];

        foreach ($customerNames as $index => $name) {
            $customers[] = Customer::create([
                'name' => $name,
                'email' => Str::slug($name) . '@example.com',
                'phone' => '01' . rand(100000000, 999999999),
                'address' => 'Address ' . ($index + 1) . ', Cairo, Egypt',
                'assigned_to' => $salesAgents[array_rand($salesAgents)]->id,
                'last_contacted_at' => now()->subDays(rand(1, 30)),
                'next_followup_at' => now()->addDays(rand(1, 7)),
            ]);
        }

        // 5. Create Leads
        $this->command->info('📋 Creating leads...');
        $leadStages = ['new', 'contacted', 'follow-up', 'proposal', 'won', 'lost'];
        $leadSources = ['facebook', 'website', 'referral', 'other'];

        for ($i = 0; $i < 50; $i++) {
            $stage = $leadStages[array_rand($leadStages)];
            $source = $leadSources[array_rand($leadSources)];
            $assignedTo = $salesAgents[array_rand($salesAgents)];
            $customer = rand(0, 1) ? $customers[array_rand($customers)] : null;

            $lead = Lead::create([
                'name' => fake('ar_SA')->name(),
                'email' => fake()->unique()->email(),
                'phone' => '01' . rand(100000000, 999999999),
                'source' => $source,
                'stage' => $stage,
                'notes' => fake('ar_SA')->text(200),
                'assigned_to' => $assignedTo->id,
                'customer_id' => $customer?->id,
                'last_contacted_at' => $stage !== 'new' ? now()->subDays(rand(1, 15)) : null,
                'next_followup_at' => in_array($stage, ['contacted', 'follow-up', 'proposal']) ? now()->addDays(rand(1, 5)) : null,
            ]);

            // Attach random tags
            if (rand(0, 1) && count($leadTags) > 0) {
                $tagCount = rand(1, min(3, count($leadTags)));
                $randomTags = collect($leadTags)->random($tagCount);
                $tagIds = collect($randomTags)->pluck('id')->toArray();
                $lead->tags()->attach($tagIds);
            }
        }

        // 6. Create Units
        $this->command->info('🏢 Creating units...');
        $locations = [
            'New Cairo',
            'Maadi',
            'Zamalek',
            'Heliopolis',
            'Nasr City',
            '6th October',
            'New Administrative Capital',
            'North Coast',
            'Sharm El Sheikh',
            'El Gouna',
            'Dahab',
            'Marina'
        ];

        $unitStatuses = ['available', 'available', 'available', 'reserved', 'sold']; // More available

        for ($i = 0; $i < 30; $i++) {
            $location = $locations[array_rand($locations)];
            $status = $unitStatuses[array_rand($unitStatuses)];
            $area = rand(80, 300);
            $rooms = rand(1, 5);
            $price = rand(500000, 5000000);

            $unit = Unit::create([
                'code' => 'UNIT-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'location' => $location,
                'area' => $area,
                'rooms' => $rooms,
                'price' => $price,
                'status' => $status,
                'description' => fake('ar_SA')->text(300),
                'reserved_by' => $status === 'reserved' ? $customers[array_rand($customers)]->id : null,
                'sold_to' => $status === 'sold' ? $customers[array_rand($customers)]->id : null,
                'reserved_at' => $status === 'reserved' ? now()->subDays(rand(1, 30)) : null,
                'sold_at' => $status === 'sold' ? now()->subDays(rand(1, 90)) : null,
            ]);

            // Attach random features
            if (count($unitFeatures) > 0) {
                $featureCount = rand(3, min(7, count($unitFeatures)));
                $randomFeatures = collect($unitFeatures)->random($featureCount);
                $featureIds = collect($randomFeatures)->pluck('id')->toArray();
                $unit->features()->attach($featureIds);
            }

            // Create unit images (placeholder paths)
            $imageCount = rand(1, 5);
            for ($j = 0; $j < $imageCount; $j++) {
                UnitImage::create([
                    'unit_id' => $unit->id,
                    'image_path' => 'units/placeholder-unit-' . ($j + 1) . '.jpg',
                    'is_primary' => $j === 0,
                    'order' => $j,
                ]);
            }

            // Create activity log
            if ($status === 'reserved') {
                UnitActivityLog::create([
                    'unit_id' => $unit->id,
                    'user_id' => $unitsManager->id,
                    'action' => 'reserved',
                    'description' => 'Unit reserved by customer',
                    'new_data' => ['status' => 'reserved', 'reserved_by' => $unit->reserved_by],
                ]);
            } elseif ($status === 'sold') {
                UnitActivityLog::create([
                    'unit_id' => $unit->id,
                    'user_id' => $unitsManager->id,
                    'action' => 'sold',
                    'description' => 'Unit sold',
                    'new_data' => ['status' => 'sold', 'sold_to' => $unit->sold_to],
                ]);
            }
        }

        // 7. Create Appointments
        $this->command->info('📅 Creating appointments...');
        for ($i = 0; $i < 25; $i++) {
            $customer = $customers[array_rand($customers)];
            $unit = Unit::where('status', '!=', 'sold')->inRandomOrder()->first();
            $user = $salesAgents[array_rand($salesAgents)];
            $statuses = ['scheduled', 'scheduled', 'scheduled', 'completed', 'cancelled'];

            Appointment::create([
                'customer_id' => $customer->id,
                'unit_id' => rand(0, 1) ? $unit?->id : null,
                'user_id' => $user->id,
                'appointment_date' => now()->addDays(rand(-7, 30))->addHours(rand(9, 17)),
                'price' => $unit ? $unit->price : rand(100000, 2000000),
                'notes' => fake('ar_SA')->text(150),
                'status' => $statuses[array_rand($statuses)],
            ]);
        }

        // 8. Create Customer Communications
        $this->command->info('📞 Creating communications...');
        $communicationTypes = ['whatsapp', 'email', 'visit', 'call'];

        foreach ($customers as $customer) {
            $communicationCount = rand(2, 8);
            for ($i = 0; $i < $communicationCount; $i++) {
                $completed = rand(0, 1);
                CustomerCommunication::create([
                    'customer_id' => $customer->id,
                    'user_id' => $salesAgents[array_rand($salesAgents)]->id,
                    'type' => $communicationTypes[array_rand($communicationTypes)],
                    'notes' => fake('ar_SA')->text(200),
                    'scheduled_at' => $completed ? now()->subDays(rand(1, 30)) : now()->addDays(rand(1, 7)),
                    'completed_at' => $completed ? now()->subDays(rand(0, 25)) : null,
                ]);
            }

            // Update customer last_contacted_at
            if ($customer->communications()->whereNotNull('completed_at')->exists()) {
                $customer->update([
                    'last_contacted_at' => $customer->communications()
                        ->whereNotNull('completed_at')
                        ->latest('completed_at')
                        ->first()?->completed_at
                ]);
            }
        }

        $this->command->info('✅ Demo data created successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Users: ' . User::count());
        $this->command->info('   - Lead Tags: ' . LeadTag::count());
        $this->command->info('   - Unit Features: ' . UnitFeature::count());
        $this->command->info('   - Customers: ' . Customer::count());
        $this->command->info('   - Leads: ' . Lead::count());
        $this->command->info('   - Units: ' . Unit::count());
        $this->command->info('   - Appointments: ' . Appointment::count());
        $this->command->info('   - Communications: ' . CustomerCommunication::count());
    }
}
