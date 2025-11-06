<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Enhanced Database Seeding...');
        $this->command->newLine();

        // Step 1: Comprehensive Roles, Permissions & Users
        $this->command->info('1️⃣  Setting up Comprehensive Role & User System...');
        $this->call(MasterRoleUserSeeder::class);
        $this->command->newLine();

        // Step 2: Clients
        $this->command->info('2️⃣  Seeding Clients...');
        $this->call(ClientSeeder::class);
        $this->command->newLine();

        // Step 3: Projects
        $this->command->info('3️⃣  Seeding Projects...');
        $this->call(ProjectSeeder::class);
        $this->command->newLine();

        // Step 4: Incomes
        $this->command->info('4️⃣  Seeding Incomes...');
        $this->call(IncomeSeeder::class);
        $this->command->newLine();

        // Step 5: Expenses
        $this->command->info('5️⃣  Seeding Expenses...');
        $this->call(ExpenseSeeder::class);
        $this->command->newLine();

        // Step 6: Workers
        $this->command->info('6️⃣  Seeding Workers...');
        $this->call(WorkerSeeder::class);
        $this->command->newLine();

        // Step 7: Employees
        $this->command->info('7️⃣  Seeding Employees...');
        $this->call(EmployeeSeeder::class);
        $this->command->newLine();

        // Step 8: Sample Tenants (Multi-tenant System)
        $this->command->info('8️⃣  Seeding Sample Tenants...');
        $this->call(SampleTenantsSeeder::class);
        $this->command->newLine();

        // Step 9: Chart of Accounts
        $this->command->info('9️⃣  Seeding Chart of Accounts...');
        $this->call(AccountsSeeder::class);
        $this->command->newLine();

        // Step 10: Sample Tasks (if not already seeded)
        if (!\App\Models\Task::exists()) {
            $this->command->info('🔟 Seeding Sample Tasks...');
            $this->call(SampleTasksSeeder::class);
            $this->command->newLine();
        }

        $this->command->info('✅ Enhanced Database Seeding Completed Successfully!');
        $this->command->newLine();
        
        if (!app()->environment('production')) {
            $this->command->info('🔐 Enhanced Development Login Credentials:');
            $this->command->table(
                ['Role', 'Email', 'Password', 'Access Level'],
                [
                    ['Super Admin', 'superadmin@siteledger.com', 'SuperSecure123!', 'Complete System Access'],
                    ['Admin', 'admin@siteledger.com', 'SecureAdmin123!', 'Full Business Access'],
                    ['Admin', 'gashumba@siteledger.com', 'password', 'Full Business Access'],
                    ['Manager', 'manager@siteledger.com', 'SecureManager123!', 'Project Management'],
                    ['Accountant', 'accountant@siteledger.com', 'SecureAccountant123!', 'Financial Management'],
                    ['Employee', 'employee@siteledger.com', 'SecureEmployee123!', 'Core Features Only'],
                    ['Client', 'client@abccorp.com', 'SecureClient123!', 'Limited Client Access'],
                    ['Viewer', 'auditor@siteledger.com', 'SecureViewer123!', 'Read-Only Access'],
                ]
            );
            $this->command->info('');
            $this->command->info('🎯 Test the enhanced sidebar with different user roles!');
            $this->command->info('Each role will show different sections based on their permissions.');
        } else {
            $this->command->info('🔒 Production environment - Only essential admin users created.');
            $this->command->info('Create additional users through the admin panel with secure credentials.');
        }
    }
}
