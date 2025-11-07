<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Income;
use App\Models\Project;
use App\Models\Tenant;

class IncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();
        
        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Please run ProjectSeeder first.');
            return;
        }

        // Get the first tenant
        $tenant = Tenant::first();
        
        if (!$tenant) {
            $this->command->warn('No tenant found. Please ensure tenants are created first.');
            return;
        }

        // Get actual project IDs
        $project1 = $projects->where('name', 'Kigali Heights Apartment Complex')->first();
        $project2 = $projects->where('name', 'Nyarugenge Commercial Center')->first();
        $project3 = $projects->where('name', 'Kimihurura Villa Project')->first();
        $project4 = $projects->where('name', 'Gacuriro Housing Estate')->first();
        $project6 = $projects->where('name', 'Remera Market Renovation')->first();

        if (!$project1 || !$project2 || !$project3 || !$project4 || !$project6) {
            $this->command->warn('Some projects not found. Skipping income seeding.');
            return;
        }

        $incomes = [
            // Kigali Heights - Advance payment
            [
                'project_id' => $project1->id,
                'invoice_number' => 'INV-2025-001',
                'amount_received' => 200000000,
                'amount_remaining' => 300000000,
                'payment_status' => 'Paid',
                'received_at' => now()->subMonths(3),
                'notes' => 'Initial advance payment for Kigali Heights project - Rwanda Construction Ltd - Bank Transfer',
            ],
            // Nyarugenge Commercial - Multiple payments
            [
                'project_id' => $project2->id,
                'invoice_number' => 'INV-2025-002',
                'amount_received' => 250000000,
                'amount_remaining' => 550000000,
                'payment_status' => 'Paid',
                'received_at' => now()->subMonths(6),
                'notes' => 'Advance payment for Commercial Center - Prime Properties Rwanda - Bank Transfer',
            ],
            [
                'project_id' => $project2->id,
                'invoice_number' => 'INV-2025-003',
                'amount_received' => 150000000,
                'amount_remaining' => 400000000,
                'payment_status' => 'Paid',
                'received_at' => now()->subMonths(3),
                'notes' => 'Milestone 1 payment - Prime Properties Rwanda - Bank Transfer',
            ],
            // Kimihurura Villa - Final payment
            [
                'project_id' => $project3->id,
                'invoice_number' => 'INV-2024-050',
                'amount_received' => 175000000,
                'amount_remaining' => 175000000,
                'payment_status' => 'Paid',
                'received_at' => now()->subMonths(12),
                'notes' => 'Initial payment - Horizon Developers - Bank Transfer',
            ],
            [
                'project_id' => $project3->id,
                'invoice_number' => 'INV-2025-004',
                'amount_received' => 175000000,
                'amount_remaining' => 0,
                'payment_status' => 'Paid',
                'received_at' => now()->subMonth(),
                'notes' => 'Final payment upon completion - Horizon Developers - Bank Transfer',
            ],
            // Gacuriro Housing - Advance
            [
                'project_id' => $project4->id,
                'invoice_number' => 'INV-2025-005',
                'amount_received' => 150000000,
                'amount_remaining' => 450000000,
                'payment_status' => 'Paid',
                'received_at' => now()->subMonth(),
                'notes' => 'Advance payment for housing estate - Green Valley Estates - Bank Transfer',
            ],
            // Remera Market
            [
                'project_id' => $project6->id,
                'invoice_number' => 'INV-2025-006',
                'amount_received' => 125000000,
                'amount_remaining' => 125000000,
                'payment_status' => 'Paid',
                'received_at' => now()->subMonths(2),
                'notes' => 'Initial payment for market renovation - City Plaza Developers - Bank Transfer',
            ],
            // Recent payments (this month)
            [
                'project_id' => $project1->id,
                'invoice_number' => 'INV-2025-007',
                'amount_received' => 50000000,
                'amount_remaining' => 250000000,
                'payment_status' => 'Paid',
                'received_at' => now()->subDays(5),
                'notes' => 'Progress payment - Rwanda Construction Ltd - Bank Transfer',
            ],
        ];

        foreach ($incomes as $income) {
            $income['tenant_id'] = $tenant->id;
            Income::firstOrCreate(
                ['invoice_number' => $income['invoice_number'], 'tenant_id' => $income['tenant_id']],
                $income
            );
        }

        $this->command->info('Incomes seeded successfully!');
    }
}
