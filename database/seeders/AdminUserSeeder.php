<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Creating Admin Users with Full Permissions...');
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Ensure admin role exists and has ALL permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->ensureAdminHasAllPermissions($adminRole);

        // Create admin user with full permissions
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@siteledger.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@siteledger.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Ensure admin role assignment and full permissions
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        // Create additional admin users if needed
        $secondaryAdmin = User::firstOrCreate(
            ['email' => 'gashumba@siteledger.com'],
            [
                'name' => 'Gashumba Admin',
                'email' => 'gashumba@siteledger.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        if (!$secondaryAdmin->hasRole('admin')) {
            $secondaryAdmin->assignRole('admin');
        }

        // Verify admin permissions
        $adminPermissionCount = $adminRole->permissions()->count();
        $totalPermissionCount = Permission::count();
        
        $this->command->info("✅ Admin role has {$adminPermissionCount}/{$totalPermissionCount} permissions");
        
        if ($adminPermissionCount === $totalPermissionCount) {
            $this->command->info('✅ Admin has ALL permissions!');
        } else {
            $this->command->warn('⚠️  Admin missing some permissions - re-syncing...');
            $this->ensureAdminHasAllPermissions($adminRole);
        }

        $this->command->info('✅ Admin users created successfully:');
        $this->command->info('- admin@siteledger.com (password: admin123)');
        $this->command->info('- gashumba@siteledger.com (password: password)');
        $this->command->info('🔒 Both users have FULL ADMINISTRATIVE ACCESS');
    }

    /**
     * Ensure admin role has all available permissions
     */
    private function ensureAdminHasAllPermissions(Role $adminRole): void
    {
        $allPermissions = Permission::all();
        
        if ($allPermissions->isEmpty()) {
            $this->command->warn('⚠️  No permissions found. Run RolePermissionSeeder first.');
            return;
        }
        
        // Sync ALL permissions to admin role
        $adminRole->syncPermissions($allPermissions->pluck('name'));
        
        $this->command->info("✅ Synced {$allPermissions->count()} permissions to admin role");
    }
}