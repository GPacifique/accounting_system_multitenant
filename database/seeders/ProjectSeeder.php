<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Client;
use App\Models\Tenant;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        
        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Please run ClientSeeder first.');
            return;
        }

        // Get the first tenant (created by ClientSeeder)
        $tenant = Tenant::first();
        
        if (!$tenant) {
            $this->command->warn('No tenant found. Please ensure tenants are created first.');
            return;
        }

        $projects = [
            [
                'name' => 'Kigali Heights Apartment Complex',
                'notes' => 'Construction of 50-unit residential apartment building in Kigali Heights',
                'contract_value' => 500000000, // 500M RWF
                'amount_paid' => 200000000,
                'amount_remaining' => 300000000,
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(9),
                'status' => 'active',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'Nyarugenge Commercial Center',
                'notes' => 'Modern shopping mall with 100 retail spaces',
                'contract_value' => 800000000, // 800M RWF
                'amount_paid' => 400000000,
                'amount_remaining' => 400000000,
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addMonths(12),
                'status' => 'active',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'Kimihurura Villa Project',
                'notes' => 'Luxury villa construction in Kimihurura district',
                'contract_value' => 350000000, // 350M RWF
                'amount_paid' => 350000000,
                'amount_remaining' => 0,
                'start_date' => now()->subMonths(12),
                'end_date' => now()->subMonth(),
                'status' => 'completed',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'Gacuriro Housing Estate',
                'notes' => '20 modern townhouses in Gacuriro',
                'contract_value' => 600000000, // 600M RWF
                'amount_paid' => 150000000,
                'amount_remaining' => 450000000,
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(18),
                'status' => 'active',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'Kacyiru Office Complex',
                'notes' => 'State-of-the-art office building with 15 floors',
                'contract_value' => 1200000000, // 1.2B RWF
                'amount_paid' => 0,
                'amount_remaining' => 1200000000,
                'start_date' => now()->addMonth(),
                'end_date' => now()->addMonths(24),
                'status' => 'pending',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'Remera Market Renovation',
                'notes' => 'Complete renovation of Remera market facilities',
                'contract_value' => 250000000, // 250M RWF
                'amount_paid' => 125000000,
                'amount_remaining' => 125000000,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(4),
                'status' => 'active',
                'tenant_id' => $tenant->id,
            ],
        ];

        foreach ($projects as $index => $projectData) {
            $projectData['client_id'] = $clients[$index % $clients->count()]->id;
            
            Project::firstOrCreate(
                ['name' => $projectData['name'], 'tenant_id' => $projectData['tenant_id']],
                $projectData
            );
        }

        $this->command->info('Projects seeded successfully!');
    }
}
