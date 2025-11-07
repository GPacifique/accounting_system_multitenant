<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Tenant;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first tenant
        $tenant = Tenant::first();
        
        if (!$tenant) {
            $this->command->warn('No tenant found. Please ensure tenants are created first.');
            return;
        }

        $employees = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Mukamana',
                'email' => 'alice.mukamana@siteledger.com',
                'phone' => '+250788100200',
                'position' => 'Project Manager',
                'department' => 'Operations',
                'salary' => 1200000,
                'date_of_joining' => now()->subYears(3),
            ],
            [
                'first_name' => 'Bernard',
                'last_name' => 'Niyonshuti',
                'email' => 'bernard.n@siteledger.com',
                'phone' => '+250788200300',
                'position' => 'Senior Accountant',
                'department' => 'Finance',
                'salary' => 1000000,
                'date_of_joining' => now()->subYears(2),
            ],
            [
                'first_name' => 'Christine',
                'last_name' => 'Uwera',
                'email' => 'christine.u@siteledger.com',
                'phone' => '+250788300400',
                'position' => 'HR Manager',
                'department' => 'Human Resources',
                'salary' => 900000,
                'date_of_joining' => now()->subYears(2),
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Mugabo',
                'email' => 'david.m@siteledger.com',
                'phone' => '+250788400500',
                'position' => 'Procurement Officer',
                'department' => 'Procurement',
                'salary' => 800000,
                'date_of_joining' => now()->subYear(),
            ],
            [
                'first_name' => 'Esther',
                'last_name' => 'Uwase',
                'email' => 'esther.u@siteledger.com',
                'phone' => '+250788500600',
                'position' => 'Administrative Assistant',
                'department' => 'Administration',
                'salary' => 600000,
                'date_of_joining' => now()->subYear(),
            ],
            [
                'first_name' => 'Frank',
                'last_name' => 'Nkubana',
                'email' => 'frank.n@siteledger.com',
                'phone' => '+250788600700',
                'position' => 'Site Engineer',
                'department' => 'Engineering',
                'salary' => 1100000,
                'date_of_joining' => now()->subMonths(18),
            ],
            [
                'first_name' => 'Grace',
                'last_name' => 'Uwimana',
                'email' => 'grace.u@siteledger.com',
                'phone' => '+250788700800',
                'position' => 'Quality Control Officer',
                'department' => 'Quality Assurance',
                'salary' => 850000,
                'date_of_joining' => now()->subMonths(14),
            ],
            [
                'first_name' => 'Henry',
                'last_name' => 'Gashumba',
                'email' => 'henry.g@siteledger.com',
                'phone' => '+250788800900',
                'position' => 'Safety Officer',
                'department' => 'Safety',
                'salary' => 750000,
                'date_of_joining' => now()->subMonths(10),
            ],
        ];

        foreach ($employees as $employee) {
            $employee['tenant_id'] = $tenant->id;
            Employee::firstOrCreate(
                ['email' => $employee['email'], 'tenant_id' => $employee['tenant_id']],
                $employee
            );
        }

        $this->command->info('Employees seeded successfully!');
    }
}
