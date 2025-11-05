<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seed the production database with essential RBAC components only.
     */
    public function run(): void
    {
        $this->command->info('🚀 Production Database Seeding...');
        $this->command->info('=====================================');
        $this->command->newLine();

        // Essential seeders for production
        $this->command->info('🛡️  Setting up roles and permissions...');
        $this->call(RolePermissionSeeder::class);
        $this->command->newLine();

        $this->command->info('👤 Creating admin users...');
        $this->call(AdminUserSeeder::class);
        $this->command->newLine();

        // Skip test data in production
        if (!app()->environment('production')) {
            $this->command->info('🧪 Adding development test data...');
            $this->call(UserSeeder::class);
            $this->call(ClientSeeder::class);
            $this->call(ProjectSeeder::class);
            $this->call(IncomeSeeder::class);
            $this->call(ExpenseSeeder::class);
            $this->call(WorkerSeeder::class);
            $this->call(EmployeeSeeder::class);
        } else {
            $this->command->info('🔒 Production environment - skipping test data');
        }

        $this->command->newLine();
        $this->command->info('✅ Production seeding completed successfully!');
        $this->command->newLine();
        
        $this->command->info('🔐 Admin Login Credentials:');
        $this->command->table(
            ['Email', 'Password', 'Role'],
            [
                ['admin@siteledger.com', 'admin123', 'Admin'],
                ['gashumba@siteledger.com', 'password', 'Admin'],
            ]
        );
        
        $this->command->newLine();
        $this->command->warn('🚨 SECURITY REMINDER:');
        $this->command->line('Please change default admin passwords after first login!');
    }
}