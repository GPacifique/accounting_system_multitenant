<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GrantAdminExpensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Granting expense permissions to admin roles...');

        // Ensure permission cache is cleared
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $expensePermissions = [
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',
            'expenses.export',
            'expenses.approve',
        ];

        foreach ($expensePermissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $roles = Role::whereIn('name', ['admin', 'super-admin'])->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($expensePermissions);
            $this->command->info("Updated role: {$role->name}");
        }

        $this->command->info('✅ Expense permissions granted to admin roles.');
    }
}
