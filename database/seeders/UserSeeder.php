<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create test users in development environments
        if (!app()->environment('production')) {
            // Admin User
            $admin = User::firstOrCreate(
                ['email' => 'admin@siteledger.com'],
                [
                    'name' => 'System Administrator',
                    'password' => Hash::make('SecureAdmin123!'),
                    'email_verified_at' => now(),
                ]
            );
            $admin->assignRole('admin');

            // Manager User
            $manager = User::firstOrCreate(
                ['email' => 'manager@siteledger.com'],
                [
                    'name' => 'Project Manager',
                    'password' => Hash::make('SecureManager123!'),
                    'email_verified_at' => now(),
                ]
            );
            $manager->assignRole('manager');

            // Accountant User
            $accountant = User::firstOrCreate(
                ['email' => 'accountant@siteledger.com'],
                [
                    'name' => 'Chief Accountant',
                    'password' => Hash::make('SecureAccountant123!'),
                    'email_verified_at' => now(),
                ]
            );
            $accountant->assignRole('accountant');

            // Regular User
            $user = User::firstOrCreate(
                ['email' => 'user@siteledger.com'],
                [
                    'name' => 'Regular User',
                    'password' => Hash::make('SecureUser123!'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('user');

            $this->command->info('Test users created for development environment.');
            $this->command->info('Login Credentials:');
            $this->command->info('Admin: admin@siteledger.com / SecureAdmin123!');
            $this->command->info('Manager: manager@siteledger.com / SecureManager123!');
            $this->command->info('Accountant: accountant@siteledger.com / SecureAccountant123!');
            $this->command->info('User: user@siteledger.com / SecureUser123!');
        } else {
            $this->command->info('Skipping test user creation in production environment.');
            $this->command->info('Create production users manually with secure credentials.');
        }
    }
}
