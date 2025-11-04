<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\Project;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::with('client')->get();
        
        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Please run ProjectSeeder first.');
            return;
        }

        // Get actual projects
        $project1 = $projects->where('name', 'Kigali Heights Apartment Complex')->first();
        $project2 = $projects->where('name', 'Nyarugenge Commercial Center')->first();
        $project3 = $projects->where('name', 'Kimihurura Villa Project')->first();
        $project4 = $projects->where('name', 'Gacuriro Housing Estate')->first();

        if (!$project1 || !$project2 || !$project3 || !$project4) {
            $this->command->warn('Some projects not found. Skipping expense seeding.');
            return;
        }

        $expenses = [
            // Material expenses
            [
                'project_id' => $project1->id,
                'client_id' => $project1->client_id,
                'category' => 'Materials',
                'amount' => 50000000,
                'description' => 'Cement and steel for foundation - Rwanda Building Materials',
                'date' => now()->subMonths(3),
                'method' => 'Bank Transfer',
            ],
            [
                'project_id' => $project1->id,
                'client_id' => $project1->client_id,
                'category' => 'Materials',
                'amount' => 30000000,
                'description' => 'Bricks and blocks - Kigali Brick Factory',
                'date' => now()->subMonths(2),
                'method' => 'Bank Transfer',
            ],
            [
                'project_id' => $project2->id,
                'client_id' => $project2->client_id,
                'category' => 'Materials',
                'amount' => 80000000,
                'description' => 'Glass and aluminum for facade - Modern Glass Rwanda',
                'date' => now()->subMonths(4),
                'method' => 'Bank Transfer',
            ],
            // Labor expenses
            [
                'project_id' => $project1->id,
                'client_id' => $project1->client_id,
                'category' => 'Labor',
                'amount' => 25000000,
                'description' => 'Construction workers monthly wages',
                'date' => now()->subMonth(),
                'method' => 'Cash',
            ],
            [
                'project_id' => $project2->id,
                'client_id' => $project2->client_id,
                'category' => 'Labor',
                'amount' => 35000000,
                'description' => 'Skilled workers and technicians',
                'date' => now()->subMonth(),
                'method' => 'Cash',
            ],
            // Equipment expenses
            [
                'project_id' => $project1->id,
                'client_id' => $project1->client_id,
                'category' => 'Equipment',
                'amount' => 15000000,
                'description' => 'Excavator and crane rental - Heavy Equipment Rental',
                'date' => now()->subMonths(2),
                'method' => 'Bank Transfer',
            ],
            [
                'project_id' => $project2->id,
                'client_id' => $project2->client_id,
                'category' => 'Equipment',
                'amount' => 20000000,
                'description' => 'Construction machinery rental - Heavy Equipment Rental',
                'date' => now()->subMonths(3),
                'method' => 'Bank Transfer',
            ],
            // Utilities
            [
                'project_id' => $project1->id,
                'client_id' => $project1->client_id,
                'category' => 'Utilities',
                'amount' => 2000000,
                'description' => 'Water and electricity for site - EUCL',
                'date' => now()->subMonth(),
                'method' => 'Mobile Money',
            ],
            [
                'project_id' => $project2->id,
                'client_id' => $project2->client_id,
                'category' => 'Utilities',
                'amount' => 3000000,
                'description' => 'Site utilities - EUCL',
                'date' => now()->subMonth(),
                'method' => 'Mobile Money',
            ],
            // Transport
            [
                'project_id' => $project1->id,
                'client_id' => $project1->client_id,
                'category' => 'Transport',
                'amount' => 5000000,
                'description' => 'Material delivery and logistics - Transport Services Ltd',
                'date' => now()->subWeeks(2),
                'method' => 'Bank Transfer',
            ],
            // Completed project expenses
            [
                'project_id' => $project3->id,
                'client_id' => $project3->client_id,
                'category' => 'Materials',
                'amount' => 120000000,
                'description' => 'All construction materials - Rwanda Building Materials',
                'date' => now()->subMonths(10),
                'method' => 'Bank Transfer',
            ],
            [
                'project_id' => $project3->id,
                'client_id' => $project3->client_id,
                'category' => 'Labor',
                'amount' => 80000000,
                'description' => 'Total labor costs',
                'date' => now()->subMonths(8),
                'method' => 'Cash',
            ],
            // Recent expenses (this week)
            [
                'project_id' => $project1->id,
                'client_id' => $project1->client_id,
                'category' => 'Materials',
                'amount' => 8000000,
                'description' => 'Paint and finishing materials - Paint Supplies Rwanda',
                'date' => now()->subDays(3),
                'method' => 'Bank Transfer',
            ],
            [
                'project_id' => $project4->id,
                'client_id' => $project4->client_id,
                'category' => 'Materials',
                'amount' => 12000000,
                'description' => 'Initial site materials - Rwanda Building Materials',
                'date' => now()->subDays(5),
                'method' => 'Bank Transfer',
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense);
        }

        $this->command->info('Expenses seeded successfully!');
    }
}
