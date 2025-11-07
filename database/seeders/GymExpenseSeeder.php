<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;

class GymExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💸 Creating Sample Gym Expenses...');

        // Get the first tenant
        $tenant = Tenant::first();
        
        if (!$tenant) {
            $this->command->warn('No tenant found. Please ensure tenants are created first.');
            return;
        }

        // Get a user to assign as the recorder
        $user = User::first();

        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $gymExpenses = [
            // Equipment Expenses
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(20),
                'category' => 'equipment',
                'description' => 'New treadmill purchase - ProForm SMART Pro 9000',
                'amount' => 2500.00,
                'payment_method' => 'bank_transfer',
                'vendor' => 'FitnessZone Rwanda',
                'receipt_number' => 'FZ-2024-001',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Replacing old treadmill in cardio section',
            ],
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(15),
                'category' => 'equipment',
                'description' => 'Dumbbells set (5kg-50kg) complete set',
                'amount' => 800.00,
                'payment_method' => 'cash',
                'vendor' => 'Gym Equipment Rwanda',
                'receipt_number' => 'GER-2024-045',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Expanding weight training area',
            ],

            // Utilities
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(5),
                'category' => 'utilities',
                'description' => 'Monthly electricity bill',
                'amount' => 450.00,
                'payment_method' => 'bank_transfer',
                'vendor' => 'EUCL (Rwanda Energy Group)',
                'receipt_number' => 'EUCL-2024-11-001',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'November 2024 electricity consumption',
            ],
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(8),
                'category' => 'utilities',
                'description' => 'Water and sewerage services',
                'amount' => 120.00,
                'payment_method' => 'cash',
                'vendor' => 'WASAC Ltd',
                'receipt_number' => 'WASAC-2024-11-078',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Monthly water bill for gym facilities',
            ],

            // Staff Salaries
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(1),
                'category' => 'staff_salary',
                'description' => 'Monthly salaries for gym trainers',
                'amount' => 1800.00,
                'payment_method' => 'bank_transfer',
                'vendor' => 'Internal Payroll',
                'receipt_number' => 'SAL-2024-11-001',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Salaries for 3 full-time trainers',
            ],
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(1),
                'category' => 'staff_salary',
                'description' => 'Front desk staff salary',
                'amount' => 600.00,
                'payment_method' => 'bank_transfer',
                'vendor' => 'Internal Payroll',
                'receipt_number' => 'SAL-2024-11-002',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Reception and customer service staff',
            ],

            // Maintenance
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(12),
                'category' => 'maintenance',
                'description' => 'Air conditioning system maintenance',
                'amount' => 200.00,
                'payment_method' => 'cash',
                'vendor' => 'Cool Tech Services',
                'receipt_number' => 'CTS-2024-156',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Quarterly AC maintenance and filter replacement',
            ],
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(7),
                'category' => 'maintenance',
                'description' => 'Equipment maintenance - elliptical machines',
                'amount' => 150.00,
                'payment_method' => 'card',
                'vendor' => 'FitServ Maintenance',
                'receipt_number' => 'FSM-2024-089',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Routine maintenance on 2 elliptical machines',
            ],

            // Marketing
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(18),
                'category' => 'marketing',
                'description' => 'Social media advertising campaign',
                'amount' => 300.00,
                'payment_method' => 'card',
                'vendor' => 'Facebook Ads',
                'receipt_number' => 'FB-ADS-2024-1122',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'November membership drive campaign',
            ],
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(25),
                'category' => 'marketing',
                'description' => 'Promotional flyers printing',
                'amount' => 50.00,
                'payment_method' => 'cash',
                'vendor' => 'Print Solutions Rwanda',
                'receipt_number' => 'PSR-2024-445',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => '1000 flyers for new member special offers',
            ],

            // Insurance & Legal
            [
                'tenant_id' => $tenant->id,
                'date' => $lastMonth->copy()->subDays(10),
                'category' => 'insurance',
                'description' => 'Gym liability insurance premium',
                'amount' => 500.00,
                'payment_method' => 'bank_transfer',
                'vendor' => 'Prime Insurance Company',
                'receipt_number' => 'PIC-2024-789',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Quarterly liability insurance payment',
            ],

            // Supplies
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(14),
                'category' => 'supplies',
                'description' => 'Cleaning supplies and sanitizers',
                'amount' => 80.00,
                'payment_method' => 'cash',
                'vendor' => 'CleanCare Rwanda',
                'receipt_number' => 'CCR-2024-234',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Monthly cleaning and hygiene supplies',
            ],
            [
                'tenant_id' => $tenant->id,
                'date' => $currentMonth->copy()->subDays(9),
                'category' => 'supplies',
                'description' => 'Towels and water bottles for members',
                'amount' => 120.00,
                'payment_method' => 'card',
                'vendor' => 'Gym Essentials Ltd',
                'receipt_number' => 'GEL-2024-567',
                'status' => 'paid',
                'user_id' => $user?->id,
                'notes' => 'Member amenities restocking',
            ],
        ];

        foreach ($gymExpenses as $expenseData) {
            Expense::firstOrCreate(
                [
                    'tenant_id' => $expenseData['tenant_id'],
                    'description' => $expenseData['description'],
                    'date' => $expenseData['date']
                ],
                $expenseData
            );
        }

        $this->command->info('✅ Created ' . count($gymExpenses) . ' sample gym expenses');
        $this->command->info('   Categories: Equipment, Utilities, Staff Salaries, Maintenance, Marketing, Insurance, Supplies');
        $this->command->info('   Total Amount: $' . number_format(array_sum(array_column($gymExpenses, 'amount')), 2));
    }
}