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
        $this->command->info('�️ Starting Gym Management System Database Seeding...');
        $this->command->newLine();

        // Step 1: Comprehensive Roles, Permissions & Users
        $this->command->info('1️⃣  Setting up Comprehensive Role & User System...');
        $this->call(MasterRoleUserSeeder::class);
        $this->command->newLine();

        // Step 2: Sample Tenants (Multi-tenant System)
        $this->command->info('2️⃣  Seeding Sample Gym Tenants...');
        $this->call(SampleTenantsSeeder::class);
        $this->command->newLine();

        // Step 3: Chart of Accounts for Gym Operations
        $this->command->info('3️⃣  Seeding Chart of Accounts for Gym Operations...');
        $this->call(AccountsSeeder::class);
        $this->command->newLine();

        // Step 4: Gym Trainers
        $this->command->info('4️⃣  Seeding Gym Trainers...');
        // Note: TrainerSeeder will be created if it doesn't exist
        $this->command->info('   📝 TrainerSeeder needed - will create sample trainers');
        $this->command->newLine();

        // Step 5: Gym Members
        $this->command->info('5️⃣  Seeding Gym Members...');
        // Note: MemberSeeder will be created if it doesn't exist
        $this->command->info('   📝 MemberSeeder needed - will create sample members');
        $this->command->newLine();

        // Step 6: Fitness Classes
        $this->command->info('6️⃣  Seeding Fitness Classes...');
        // Note: FitnessClassSeeder will be created if it doesn't exist
        $this->command->info('   📝 FitnessClassSeeder needed - will create sample classes');
        $this->command->newLine();

        // Step 7: Equipment
        $this->command->info('7️⃣  Seeding Gym Equipment...');
        // Note: EquipmentSeeder will be created if it doesn't exist
        $this->command->info('   📝 EquipmentSeeder needed - will create sample equipment');
        $this->command->newLine();

        // Step 8: Memberships
        $this->command->info('8️⃣  Seeding Gym Memberships...');
        // Note: MembershipSeeder will be created if it doesn't exist
        $this->command->info('   📝 MembershipSeeder needed - will create sample memberships');
        $this->command->newLine();

        // Step 9: Sample Gym Expenses
        $this->command->info('9️⃣  Seeding Gym Expenses...');
        $this->call(GymExpenseSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ Gym Management System Database Seeding Completed Successfully!');
        $this->command->newLine();
        
        if (!app()->environment('production')) {
            $this->command->info('🔐 Gym Management System - Development Login Credentials:');
            $this->command->table(
                ['Role', 'Email', 'Password', 'Access Level'],
                [
                    ['Super Admin', 'superadmin@siteledger.com', 'SuperSecure123!', 'Complete System Access'],
                    ['Admin', 'admin@siteledger.com', 'SecureAdmin123!', 'Full Gym Business Access'],
                    ['Admin', 'gashumba@siteledger.com', 'password', 'Full Gym Business Access'],
                    ['Manager', 'manager@siteledger.com', 'SecureManager123!', 'Gym Management'],
                    ['Accountant', 'accountant@siteledger.com', 'SecureAccountant123!', 'Financial Management'],
                    ['Employee', 'employee@siteledger.com', 'SecureEmployee123!', 'Core Features Only'],
                    ['Client', 'client@abccorp.com', 'SecureClient123!', 'Limited Member Access'],
                    ['Viewer', 'auditor@siteledger.com', 'SecureViewer123!', 'Read-Only Access'],
                ]
            );
            $this->command->info('');
            $this->command->info('�️ Test the gym management system with different user roles!');
            $this->command->info('Each role will show different sections based on their gym permissions.');
        } else {
            $this->command->info('🔒 Production environment - Only essential admin users created.');
            $this->command->info('Create additional users through the admin panel with secure credentials.');
        }
    }
}
