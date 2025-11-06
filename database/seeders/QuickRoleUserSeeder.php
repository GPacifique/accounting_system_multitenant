<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class QuickRoleUserSeeder extends Seeder
{
    /**
     * Quick seeder for roles and users only (without other data)
     */
    public function run(): void
    {
        $this->command->info('⚡ Quick Role & User Setup');
        $this->command->info('========================');
        $this->command->newLine();

        // Just run the master role user seeder
        $this->call(MasterRoleUserSeeder::class);

        $this->command->info('');
        $this->command->info('⚡ Quick Role & User Setup Complete!');
        $this->command->info('🚀 Ready to test the enhanced sidebar with different user roles.');
    }
}