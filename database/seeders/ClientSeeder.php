<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Rwanda Construction Ltd',
                'contact_person' => 'Jean Paul Kagame',
                'email' => 'info@rwandaconstruction.rw',
                'phone' => '+250788123456',
                'address' => 'KN 5 Ave, Kigali',
            ],
            [
                'name' => 'Prime Properties Rwanda',
                'contact_person' => 'Marie Uwera',
                'email' => 'contact@primeproperties.rw',
                'phone' => '+250788234567',
                'address' => 'KG 7 Ave, Kigali',
            ],
            [
                'name' => 'Horizon Developers',
                'contact_person' => 'Patrick Nkunda',
                'email' => 'info@horizondev.rw',
                'phone' => '+250788345678',
                'address' => 'KN 12 St, Kigali',
            ],
            [
                'name' => 'Green Valley Estates',
                'contact_person' => 'Christine Mukamana',
                'email' => 'contact@greenvalley.rw',
                'phone' => '+250788456789',
                'address' => 'Kimihurura, Kigali',
            ],
            [
                'name' => 'City Plaza Developers',
                'contact_person' => 'David Mugisha',
                'email' => 'info@cityplaza.rw',
                'phone' => '+250788567890',
                'address' => 'Nyarugenge, Kigali',
            ],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(
                ['email' => $client['email']],
                $client
            );
        }

        $this->command->info('Clients seeded successfully!');
    }
}
