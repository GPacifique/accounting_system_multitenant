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
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();

        // Step 1: Roles and Permissions
        $this->command->info('1️⃣  Seeding Roles & Permissions...');
        $this->call(RolePermissionSeeder::class);
        $this->command->newLine();

        // Step 2: Admin Users
        $this->command->info('2️⃣  Seeding Admin Users...');
        $this->call(AdminUserSeeder::class);
        $this->command->newLine();

        // Step 3: Users
        $this->command->info('3️⃣  Seeding Users...');
        $this->call(UserSeeder::class);
        $this->command->newLine();

        // Step 4: Clients
        $this->command->info('4️⃣  Seeding Clients...');
        $this->call(ClientSeeder::class);
        $this->command->newLine();

        // Step 5: Projects
        $this->command->info('5️⃣  Seeding Projects...');
        $this->call(ProjectSeeder::class);
        $this->command->newLine();

        // Step 6: Incomes
        $this->command->info('6️⃣  Seeding Incomes...');
        $this->call(IncomeSeeder::class);
        $this->command->newLine();

        // Step 7: Expenses
        $this->command->info('7️⃣  Seeding Expenses...');
        $this->call(ExpenseSeeder::class);
        $this->command->newLine();

        // Step 8: Workers
        $this->command->info('8️⃣  Seeding Workers...');
        $this->call(WorkerSeeder::class);
        $this->command->newLine();

        // Step 9: Employees
        $this->command->info('9️⃣  Seeding Employees...');
        $this->call(EmployeeSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        
        if (!app()->environment('production')) {
            $this->command->info('🔐 Development Login Credentials:');
            $this->command->table(
                ['Role', 'Email', 'Password'],
                [
                    ['Admin', 'admin@siteledger.com', 'SecureAdmin123!'],
                    ['Manager', 'manager@siteledger.com', 'SecureManager123!'],
                    ['Accountant', 'accountant@siteledger.com', 'SecureAccountant123!'],
                    ['User', 'user@siteledger.com', 'SecureUser123!'],
                ]
            );
        } else {
            $this->command->info('🔒 Production environment detected - no test users created.');
        }
    }
}
