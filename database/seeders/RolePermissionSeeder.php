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

        // Define all permissions
        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Project Management
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            
            // Expense Management
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',
            
            // Income Management
            'incomes.view',
            'incomes.create',
            'incomes.edit',
            'incomes.delete',
            
            // Payment Management
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            
            // Report Management
            'reports.view',
            'reports.generate',
            'reports.export',
            
            // Employee/Worker Management
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',
            'workers.view',
            'workers.create',
            'workers.edit',
            'workers.delete',
            
            // Order Management
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            
            // Settings
            'settings.view',
            'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define role permissions matrix
        $roles = [
            'admin' => $permissions, // Admin has all permissions
            
            'manager' => [
                'projects.view',
                'projects.create',
                'projects.edit',
                'employees.view',
                'employees.create',
                'employees.edit',
                'workers.view',
                'workers.create',
                'workers.edit',
                'orders.view',
                'orders.create',
                'orders.edit',
                'reports.view',
                'reports.generate',
            ],
            
            'accountant' => [
                'payments.view',
                'payments.create',
                'payments.edit',
                'incomes.view',
                'incomes.create',
                'incomes.edit',
                'expenses.view',
                'expenses.create',
                'expenses.edit',
                'reports.view',
                'reports.generate',
                'reports.export',
                'projects.view', // Can view but not edit projects
            ],
            
            'user' => [
                // No permissions - users need admin approval to access system features
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
