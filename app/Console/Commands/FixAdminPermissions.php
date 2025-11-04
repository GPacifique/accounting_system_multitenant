<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:fix-permissions {--all-admins : Fix permissions for all admin users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure admin role and all admin users have full permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing Admin Permissions...');
        $this->line('================================');

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->info("✅ Admin role exists");

        // Get all permissions
        $allPermissions = Permission::all();
        if ($allPermissions->isEmpty()) {
            $this->error('❌ No permissions found! Run RolePermissionSeeder first:');
            $this->line('   php artisan db:seed --class=RolePermissionSeeder --force');
            return 1;
        }

        // Sync all permissions to admin role
        $adminRole->syncPermissions($allPermissions->pluck('name'));
        $this->info("✅ Synced {$allPermissions->count()} permissions to admin role");

        // Find admin users
        $adminUsers = User::role('admin')->get();
        if ($adminUsers->isEmpty()) {
            $this->warn('⚠️  No admin users found');
            $this->line('Create an admin user with: php artisan admin:create');
            return 0;
        }

        $this->info("Found {$adminUsers->count()} admin user(s):");
        
        foreach ($adminUsers as $adminUser) {
            $userPermissionCount = $adminUser->getAllPermissions()->count();
            $this->line("  • {$adminUser->email} - {$userPermissionCount}/{$allPermissions->count()} permissions");
            
            // Ensure user has admin role (refresh assignment)
            if (!$adminUser->hasRole('admin')) {
                $adminUser->assignRole('admin');
                $this->info("    ↳ Re-assigned admin role");
            }
        }

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->line('');
        $this->info('🎉 Admin permissions fixed successfully!');
        $this->info('All admin users now have full access to:');
        $this->line('• User Management');
        $this->line('• Project Management');
        $this->line('• Financial Management');
        $this->line('• Payment Management');
        $this->line('• Employee/Worker Management');
        $this->line('• Order Management');
        $this->line('• Report Generation');
        $this->line('• System Settings');

        return 0;
    }
}