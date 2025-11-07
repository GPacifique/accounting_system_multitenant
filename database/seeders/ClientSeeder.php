<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Tenant;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first tenant or create a default one
        $tenant = Tenant::first();
        
        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'Default Construction Company',
                'domain' => 'default-company',
                'business_type' => 'construction',
                'email' => 'admin@defaultcompany.com',
                'phone' => '+250788000000',
                'address' => 'Kigali, Rwanda',
                'subscription_plan' => 'enterprise',
                'subscription_expires_at' => now()->addYear(),
                'status' => 'active',
                'database' => 'tenant_default_company',
                'settings' => [
                    'currency' => 'RWF',
                    'timezone' => 'Africa/Kigali',
                    'language' => 'en',
                    'date_format' => 'Y-m-d',
                    'financial_year_start' => '01-01',
                ],
                'features' => [
                    'projects' => true,
                    'tasks' => true,
                    'finance' => true,
                    'reports' => true,
                    'team_management' => true,
                    'advanced_analytics' => true,
                ],
                'max_users' => 100,
            ]);
            $this->command->info('Created default tenant for seeding.');
        }

        $clients = [
            [
                'name' => 'Rwanda Construction Ltd',
                'contact_person' => 'Jean Paul Kagame',
                'email' => 'info@rwandaconstruction.rw',
                'phone' => '+250788123456',
                'address' => 'KN 5 Ave, Kigali',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'Prime Properties Rwanda',
                'contact_person' => 'Marie Uwera',
                'email' => 'contact@primeproperties.rw',
                'phone' => '+250788234567',
                'address' => 'KG 7 Ave, Kigali',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'Horizon Developers',
                'contact_person' => 'Patrick Nkunda',
                'email' => 'info@horizondev.rw',
                'phone' => '+250788345678',
                'address' => 'KN 12 St, Kigali',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'Green Valley Estates',
                'contact_person' => 'Christine Mukamana',
                'email' => 'contact@greenvalley.rw',
                'phone' => '+250788456789',
                'address' => 'Kimihurura, Kigali',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'City Plaza Developers',
                'contact_person' => 'David Mugisha',
                'email' => 'info@cityplaza.rw',
                'phone' => '+250788567890',
                'address' => 'Nyarugenge, Kigali',
                'tenant_id' => $tenant->id,
            ],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(
                ['email' => $client['email'], 'tenant_id' => $client['tenant_id']],
                $client
            );
        }

        $this->command->info('Clients seeded successfully!');
    }
}
