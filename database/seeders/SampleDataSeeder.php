<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Employee;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample data for dashboard testing...');

        // Create sample projects
        if (Schema::hasTable('projects')) {
            $projects = [
                [
                    'name' => 'Downtown Office Complex',
                    'description' => 'Modern 20-story office building with retail space',
                    'contract_value' => 2500000,
                    'status' => 'active',
                    'start_date' => Carbon::now()->subDays(30),
                    'created_at' => Carbon::now()->subDays(30),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Residential Tower Phase 1',
                    'description' => '150-unit luxury residential development',
                    'contract_value' => 1800000,
                    'status' => 'active',
                    'start_date' => Carbon::now()->subDays(45),
                    'created_at' => Carbon::now()->subDays(45),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Shopping Mall Renovation',
                    'description' => 'Complete renovation of 3-story shopping center',
                    'contract_value' => 950000,
                    'status' => 'completed',
                    'start_date' => Carbon::now()->subDays(90),
                    'created_at' => Carbon::now()->subDays(90),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Bridge Construction Project',
                    'description' => 'New concrete bridge over the river',
                    'contract_value' => 3200000,
                    'status' => 'pending',
                    'start_date' => Carbon::now()->addDays(15),
                    'created_at' => Carbon::now()->subDays(10),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'School Building Extension',
                    'description' => 'Adding new wing to existing school building',
                    'contract_value' => 750000,
                    'status' => 'active',
                    'start_date' => Carbon::now()->subDays(20),
                    'created_at' => Carbon::now()->subDays(20),
                    'updated_at' => Carbon::now(),
                ],
            ];

            foreach ($projects as $project) {
                DB::table('projects')->insertOrIgnore($project);
            }

            $this->command->info('✅ Created 5 sample projects');
        }

        // Create sample incomes
        if (Schema::hasTable('incomes')) {
            $projectIds = DB::table('projects')->pluck('id')->take(5);
            
            $incomes = [
                [
                    'project_id' => $projectIds[0] ?? null,
                    'amount_received' => 500000,
                    'invoice_number' => 'INV-2024-001',
                    'received_at' => Carbon::now()->subDays(25),
                    'payment_status' => 'Paid',
                    'amount_remaining' => 0,
                    'notes' => 'Initial advance payment for Downtown Office Complex',
                    'created_at' => Carbon::now()->subDays(25),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'project_id' => $projectIds[1] ?? null,
                    'amount_received' => 360000,
                    'invoice_number' => 'INV-2024-002',
                    'received_at' => Carbon::now()->subDays(20),
                    'payment_status' => 'Paid',
                    'amount_remaining' => 0,
                    'notes' => 'First milestone payment for Residential Tower',
                    'created_at' => Carbon::now()->subDays(20),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'project_id' => $projectIds[2] ?? null,
                    'amount_received' => 950000,
                    'invoice_number' => 'INV-2024-003',
                    'received_at' => Carbon::now()->subDays(5),
                    'payment_status' => 'Paid',
                    'amount_remaining' => 0,
                    'notes' => 'Final payment for Shopping Mall Renovation',
                    'created_at' => Carbon::now()->subDays(5),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'project_id' => $projectIds[0] ?? null,
                    'amount_received' => 750000,
                    'invoice_number' => 'INV-2024-004',
                    'received_at' => Carbon::now()->subDays(10),
                    'payment_status' => 'Paid',
                    'amount_remaining' => 0,
                    'notes' => 'Second milestone payment for Downtown Office Complex',
                    'created_at' => Carbon::now()->subDays(10),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'project_id' => $projectIds[4] ?? null,
                    'amount_received' => 150000,
                    'invoice_number' => 'INV-2024-005',
                    'received_at' => Carbon::now()->subDays(15),
                    'payment_status' => 'Paid',
                    'amount_remaining' => 0,
                    'notes' => 'Advance payment for School Building Extension',
                    'created_at' => Carbon::now()->subDays(15),
                    'updated_at' => Carbon::now(),
                ],
            ];

            foreach ($incomes as $income) {
                if ($income['project_id']) {
                    DB::table('incomes')->insertOrIgnore($income);
                }
            }

            $this->command->info('✅ Created 5 sample income records');
        }

        // Create sample expenses
        if (Schema::hasTable('expenses')) {
            $expenses = [
                [
                    'amount' => 45000,
                    'description' => 'Construction materials - concrete and steel',
                    'category' => 'materials',
                    'date' => Carbon::now()->subDays(3),
                    'method' => 'bank_transfer',
                    'status' => 'paid',
                    'created_at' => Carbon::now()->subDays(3),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'amount' => 25000,
                    'description' => 'Equipment rental - excavator and crane',
                    'category' => 'equipment',
                    'date' => Carbon::now()->subDays(7),
                    'method' => 'cheque',
                    'status' => 'paid',
                    'created_at' => Carbon::now()->subDays(7),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'amount' => 18000,
                    'description' => 'Subcontractor payment - electrical work',
                    'category' => 'subcontractor',
                    'date' => Carbon::now()->subDays(12),
                    'method' => 'bank_transfer',
                    'status' => 'paid',
                    'created_at' => Carbon::now()->subDays(12),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'amount' => 8500,
                    'description' => 'Office supplies and administrative costs',
                    'category' => 'administrative',
                    'date' => Carbon::now()->subDays(5),
                    'method' => 'cash',
                    'status' => 'paid',
                    'created_at' => Carbon::now()->subDays(5),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'amount' => 32000,
                    'description' => 'Heavy machinery transportation',
                    'category' => 'transportation',
                    'date' => Carbon::now()->subDays(8),
                    'method' => 'bank_transfer',
                    'status' => 'paid',
                    'created_at' => Carbon::now()->subDays(8),
                    'updated_at' => Carbon::now(),
                ],
            ];

            foreach ($expenses as $expense) {
                DB::table('expenses')->insertOrIgnore($expense);
            }

            $this->command->info('✅ Created 5 sample expense records');
        }

        // Create sample payments
        if (Schema::hasTable('payments')) {
            $payments = [
                [
                    'amount' => 85000,
                    'method' => 'bank_transfer',
                    'reference' => 'PAY-OCT-2024-001',
                    'status' => 'completed',
                    'created_at' => Carbon::now()->subDays(2),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'amount' => 12000,
                    'method' => 'cheque',
                    'reference' => 'CHQ-2024-045',
                    'status' => 'completed',
                    'created_at' => Carbon::now()->subDays(6),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'amount' => 28000,
                    'method' => 'bank_transfer',
                    'reference' => 'TRF-2024-089',
                    'status' => 'completed',
                    'created_at' => Carbon::now()->subDays(9),
                    'updated_at' => Carbon::now(),
                ],
            ];

            foreach ($payments as $payment) {
                DB::table('payments')->insertOrIgnore($payment);
            }

            $this->command->info('✅ Created 3 sample payment records');
        }

        // Create sample workers  
        if (Schema::hasTable('workers')) {
            $workers = [
                [
                    'first_name' => 'Jean',
                    'last_name' => 'Baptiste',
                    'position' => 'Site Manager',
                    'salary_cents' => 15000000, // 150,000 RWF in cents
                    'currency' => 'RWF',
                    'status' => 'active',
                    'phone' => '+250788123456',
                    'hired_at' => Carbon::now()->subDays(30),
                    'created_at' => Carbon::now()->subDays(30),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'first_name' => 'Marie',
                    'last_name' => 'Uwimana',
                    'position' => 'Construction Supervisor',
                    'salary_cents' => 12000000, // 120,000 RWF in cents
                    'currency' => 'RWF',
                    'status' => 'active',
                    'phone' => '+250788234567',
                    'hired_at' => Carbon::now()->subDays(25),
                    'created_at' => Carbon::now()->subDays(25),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'first_name' => 'Paul',
                    'last_name' => 'Niyonzima',
                    'position' => 'Equipment Operator',
                    'salary_cents' => 8000000, // 80,000 RWF in cents
                    'currency' => 'RWF',
                    'status' => 'active',
                    'phone' => '+250788345678',
                    'hired_at' => Carbon::now()->subDays(20),
                    'created_at' => Carbon::now()->subDays(20),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'first_name' => 'Grace',
                    'last_name' => 'Mukamana',
                    'position' => 'Safety Officer',
                    'salary_cents' => 9500000, // 95,000 RWF in cents
                    'currency' => 'RWF',
                    'status' => 'active',
                    'phone' => '+250788456789',
                    'hired_at' => Carbon::now()->subDays(15),
                    'created_at' => Carbon::now()->subDays(15),
                    'updated_at' => Carbon::now(),
                ],
            ];

            foreach ($workers as $worker) {
                DB::table('workers')->insertOrIgnore($worker);
            }

            $this->command->info('✅ Created 4 sample worker records');
        }

        // Create sample employees
        if (Schema::hasTable('employees')) {
            $employees = [
                [
                    'name' => 'Alice Mutoni',
                    'position' => 'Project Coordinator',
                    'department' => 'Operations',
                    'salary' => 180000,
                    'email' => 'alice.mutoni@siteledger.com',
                    'phone' => '+250788567890',
                    'created_at' => Carbon::now()->subDays(40),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'David Habimana',
                    'position' => 'Financial Analyst',
                    'department' => 'Finance',
                    'salary' => 160000,
                    'email' => 'david.habimana@siteledger.com',
                    'phone' => '+250788678901',
                    'created_at' => Carbon::now()->subDays(35),
                    'updated_at' => Carbon::now(),
                ],
            ];

            foreach ($employees as $employee) {
                DB::table('employees')->insertOrIgnore($employee);
            }

            $this->command->info('✅ Created 2 sample employee records');
        }

        $this->command->info('🎉 Sample data creation completed successfully!');
        $this->command->info('You can now view the full dashboard with realistic data.');
    }
}