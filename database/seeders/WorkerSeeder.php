<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Worker;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workers = [
            [
                'first_name' => 'Jean Baptiste',
                'last_name' => 'Ndayisenga',
                'email' => 'jean.baptiste@worker.rw',
                'phone' => '+250788111222',
                'position' => 'Site Supervisor',
                'salary_cents' => 50000000, // 500,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonths(14),
            ],
            [
                'first_name' => 'Emmanuel',
                'last_name' => 'Hakizimana',
                'email' => 'emmanuel.h@worker.rw',
                'phone' => '+250788222333',
                'position' => 'Mason',
                'salary_cents' => 35000000, // 350,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonths(12),
            ],
            [
                'first_name' => 'Patrick',
                'last_name' => 'Nsengimana',
                'email' => 'patrick.n@worker.rw',
                'phone' => '+250788333444',
                'position' => 'Carpenter',
                'salary_cents' => 38000000, // 380,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonths(10),
            ],
            [
                'first_name' => 'Claude',
                'last_name' => 'Uwizeyimana',
                'email' => 'claude.u@worker.rw',
                'phone' => '+250788444555',
                'position' => 'Electrician',
                'salary_cents' => 42000000, // 420,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonths(8),
            ],
            [
                'first_name' => 'Olivier',
                'last_name' => 'Mugabo',
                'email' => 'olivier.m@worker.rw',
                'phone' => '+250788555666',
                'position' => 'Plumber',
                'salary_cents' => 38000000, // 380,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonths(6),
            ],
            [
                'first_name' => 'Eric',
                'last_name' => 'Niyonzima',
                'email' => 'eric.n@worker.rw',
                'phone' => '+250788666777',
                'position' => 'Welder',
                'salary_cents' => 36000000, // 360,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonths(5),
            ],
            [
                'first_name' => 'Faustin',
                'last_name' => 'Muhire',
                'email' => 'faustin.m@worker.rw',
                'phone' => '+250788777888',
                'position' => 'Painter',
                'salary_cents' => 32000000, // 320,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonths(4),
            ],
            [
                'first_name' => 'Gilbert',
                'last_name' => 'Nkurunziza',
                'email' => null,
                'phone' => '+250788888999',
                'position' => 'Laborer',
                'salary_cents' => 20000000, // 200,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonths(3),
            ],
            [
                'first_name' => 'Innocent',
                'last_name' => 'Habimana',
                'email' => null,
                'phone' => '+250788999000',
                'position' => 'Laborer',
                'salary_cents' => 20000000, // 200,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonths(2),
            ],
            [
                'first_name' => 'Jacques',
                'last_name' => 'Mutabazi',
                'email' => 'jacques.m@worker.rw',
                'phone' => '+250788000111',
                'position' => 'Driver',
                'salary_cents' => 30000000, // 300,000 RWF
                'currency' => 'RWF',
                'status' => 'active',
                'hired_at' => now()->subMonth(),
            ],
        ];

        foreach ($workers as $worker) {
            Worker::create($worker);
        }

        $this->command->info('Workers seeded successfully!');
    }
}
