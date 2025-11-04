<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email?} {password?} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user with full permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Creating Admin User with Full Permissions');
        $this->line('============================================');

        // Get user input
        $email = $this->argument('email') ?: $this->ask('Enter admin email', 'admin@siteledger.com');
        $password = $this->argument('password') ?: $this->secret('Enter admin password');
        $name = $this->argument('name') ?: $this->ask('Enter admin name', 'System Administrator');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            if (!$this->confirm("User with email {$email} already exists. Update permissions?")) {
                return 0;
            }
            $user = User::where('email', $email)->first();
            $this->info("✅ Updating existing user: {$email}");
        } else {
            // Create new user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->info("✅ Created new admin user: {$email}");
        }

        // Ensure admin role exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Assign admin role
        $user->assignRole('admin');
        
        // Verify all permissions
        $this->info('🔍 Verifying admin permissions...');
        
        $allPermissions = Permission::all();
        $userPermissions = $user->getAllPermissions();
        
        $this->line("Total permissions in system: {$allPermissions->count()}");
        $this->line("Admin user permissions: {$userPermissions->count()}");
        
        if ($userPermissions->count() === $allPermissions->count()) {
            $this->info("✅ Admin has ALL permissions!");
        } else {
            $this->warn("⚠️  Admin missing some permissions. Syncing...");
            $adminRole->syncPermissions($allPermissions->pluck('name'));
            $this->info("✅ All permissions synced to admin role!");
        }

        // Display summary
        $this->line('');
        $this->info('🎉 Admin User Setup Complete!');
        $this->table(
            ['Property', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', 'admin'],
                ['Permissions', $user->getAllPermissions()->count() . ' (ALL)'],
                ['Created/Updated', now()->format('Y-m-d H:i:s')],
            ]
        );

        $this->line('');
        $this->info('🚀 Admin can now access all features:');
        $this->line('• User Management');
        $this->line('• Project Management');
        $this->line('• Financial Management (Income/Expenses)');
        $this->line('• Payment Management');
        $this->line('• Employee/Worker Management');
        $this->line('• Order Management');
        $this->line('• Report Generation & Export');
        $this->line('• System Settings');

        return 0;
    }
}