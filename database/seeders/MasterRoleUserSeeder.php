<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterRoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Master Role & User Seeding Process...');
        $this->command->info('================================================');
        $this->command->newLine();

        // Step 1: Create comprehensive roles and permissions
        $this->command->info('1️⃣  Creating Comprehensive Roles & Permissions System...');
        $this->call(ComprehensiveRolePermissionSeeder::class);
        $this->command->newLine();

        // Step 2: Create users for all roles
        $this->command->info('2️⃣  Creating Users for All Roles...');
        $this->call(ComprehensiveUserSeeder::class);
        $this->command->newLine();

        // Summary
        $this->command->info('📊 Role & User System Summary:');
        $this->command->info('===============================');
        
        // Count roles and permissions
        $roleCount = \Spatie\Permission\Models\Role::count();
        $permissionCount = \Spatie\Permission\Models\Permission::count();
        $userCount = \App\Models\User::count();
        
        $this->command->table(
            ['Component', 'Count', 'Status'],
            [
                ['Roles', $roleCount, '✅ Complete'],
                ['Permissions', $permissionCount, '✅ Complete'],
                ['Users', $userCount, '✅ Complete'],
            ]
        );

        $this->command->info('');
        $this->command->info('🎯 Available Roles in System:');
        
        $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
        $roleTable = [];
        
        foreach ($roles as $role) {
            $userCount = \App\Models\User::role($role->name)->count();
            $roleTable[] = [
                $role->name,
                $role->permissions->count(),
                $userCount,
                $role->name === 'super-admin' ? 'System Owner' : 
                ($role->name === 'admin' ? 'Full Business Access' :
                ($role->name === 'manager' ? 'Project Management' :
                ($role->name === 'accountant' ? 'Financial Management' :
                ($role->name === 'employee' ? 'Core Features Only' :
                ($role->name === 'client' ? 'Limited Client Access' :
                ($role->name === 'viewer' ? 'Read-Only Access' : 'Standard User'))))))
            ];
        }
        
        $this->command->table(
            ['Role', 'Permissions', 'Users', 'Access Level'],
            $roleTable
        );

        if (!app()->environment('production')) {
            $this->command->info('');
            $this->command->info('🔧 Development Environment - Testing Instructions:');
            $this->command->info('================================================');
            $this->command->info('');
            $this->command->info('🧪 Test Enhanced Sidebar with Different Roles:');
            $this->command->info('');
            $this->command->info('👑 Super Admin (superadmin@siteledger.com):');
            $this->command->info('   - Full system access including tenant management');
            $this->command->info('   - Can see ALL sidebar sections');
            $this->command->info('');
            $this->command->info('🛡️  Admin (admin@siteledger.com):');
            $this->command->info('   - Full business access (no tenant management)');
            $this->command->info('   - Can see all sidebar sections except tenant management');
            $this->command->info('');
            $this->command->info('📋 Manager (manager@siteledger.com):');
            $this->command->info('   - Dashboard + Core Features + Project Management sections');
            $this->command->info('   - Cannot see Financial Management or Administration');
            $this->command->info('');
            $this->command->info('💰 Accountant (accountant@siteledger.com):');
            $this->command->info('   - Dashboard + Core Features + Financial Management sections');
            $this->command->info('   - Cannot see Project Management or Administration');
            $this->command->info('');
            $this->command->info('👤 Employee (employee@siteledger.com):');
            $this->command->info('   - Dashboard + Core Features sections only');
            $this->command->info('   - Most restricted access');
            $this->command->info('');
            $this->command->info('🏢 Client (client@abccorp.com):');
            $this->command->info('   - Limited access to their own projects and invoices');
            $this->command->info('');
            $this->command->info('👁️  Viewer (auditor@siteledger.com):');
            $this->command->info('   - Read-only access to most features');
            $this->command->info('');
            $this->command->info('🎯 All passwords follow the pattern: Secure[Role]123!');
            $this->command->info('   (except gashumba@siteledger.com uses "password")');
        }

        $this->command->info('');
        $this->command->info('✅ Master Role & User Seeding Process Complete!');
        $this->command->info('🎉 Your enhanced sidebar system is ready with comprehensive role-based access!');
        
        if (app()->environment('production')) {
            $this->command->info('');
            $this->command->info('🔒 Production Environment Notes:');
            $this->command->info('- Only essential admin users were created');
            $this->command->info('- Create additional users through the admin panel');
            $this->command->info('- Ensure secure passwords for all production users');
        }
    }
}