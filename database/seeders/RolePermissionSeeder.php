<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',
            'reports.view',
            'reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles
        $roles = [
            'admin' => $permissions, // admin has all permissions
            'manager' => [
                'projects.view',
                'projects.create',
                'projects.edit',
                'expenses.view',
                'expenses.create',
                'reports.view',
                'reports.generate',
            ],
            'user' => [
                'projects.view',
                'expenses.view',
                'reports.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
