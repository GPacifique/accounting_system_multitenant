<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'label' => 'Administrator'],
            ['name' => 'accountant', 'label' => 'Accountant'],
            ['name' => 'manager', 'label' => 'Manager'],
            ['name' => 'user', 'label' => 'Regular User'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }

        // Optional: assign roles to users by email (adjust to your users)
        $admin = User::whereEmail('admin@example.com')->first();
        if ($admin) $admin->assignRole('admin');

        $acc = User::whereEmail('accountant@example.com')->first();
        if ($acc) $acc->assignRole('accountant');

        $mgr = User::whereEmail('manager@example.com')->first();
        if ($mgr) $mgr->assignRole('manager');
    }
}
