<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        // Ensure admin role exists and assign it
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminUser->assignRole('admin');

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

        $secondaryAdmin->assignRole('admin');

        $this->command->info('Admin users created successfully:');
        $this->command->info('- admin@siteledger.com (password: admin123)');
        $this->command->info('- gashumba@siteledger.com (password: password)');
    }
}