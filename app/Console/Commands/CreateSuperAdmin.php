<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-super {email?} {password?} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin user who can manage all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Creating Super Admin User');
        $this->line('===============================');

        // Get user input
        $email = $this->argument('email') ?: $this->ask('Enter super admin email', 'superadmin@siteledger.com');
        $password = $this->argument('password') ?: $this->secret('Enter super admin password');
        $name = $this->argument('name') ?: $this->ask('Enter super admin name', 'Super Administrator');

        // Check if user already exists
        if (\App\Models\User::where('email', $email)->exists()) {
            $this->error("❌ User with email {$email} already exists!");
            return 1;
        }

        // Create super admin user
        $user = \App\Models\User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'is_super_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Assign admin role
        $user->assignRole('admin');

        $this->newLine();
        $this->info('✅ Super Admin created successfully!');
        $this->newLine();
        $this->line("📧 Email: {$email}");
        $this->line("🔑 Password: {$password}");
        $this->line("👤 Name: {$name}");
        $this->newLine();
        $this->warn('⚠️  Please change the password after first login!');
        
        return 0;
    }
}
