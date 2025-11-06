<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SampleTenantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏢 Creating sample tenants...');

        // Create sample tenants with realistic data
        $tenants = [
            [
                'name' => 'Rwanda Construction Co.',
                'domain' => 'rwanda-construction',
                'business_type' => 'construction',
                'email' => 'admin@rwandaconstruction.com',
                'phone' => '+250788123456',
                'address' => 'KG 11 Ave, Kimisagara, Kigali, Rwanda',
                'subscription_plan' => 'enterprise',
                'subscription_expires_at' => now()->addYear(),
                'status' => 'active',
                'settings' => [
                    'currency' => 'RWF',
                    'timezone' => 'Africa/Kigali',
                    'language' => 'en',
                    'date_format' => 'Y-m-d',
                    'financial_year_start' => '01-01',
                ],
                'features' => [
                    'projects' => true,
                    'tasks' => true,
                    'finance' => true,
                    'reports' => true,
                    'team_management' => true,
                    'advanced_analytics' => true,
                ],
                'max_users' => 100,
                'trial_ends_at' => null,
                'admin_users' => [
                    [
                        'name' => 'Jean Baptiste Uwimana',
                        'email' => 'jean@rwandaconstruction.com',
                        'role' => 'admin'
                    ],
                    [
                        'name' => 'Marie Claire Mukamana',
                        'email' => 'marie@rwandaconstruction.com',
                        'role' => 'manager'
                    ]
                ]
            ],
            [
                'name' => 'Kigali Tech Solutions',
                'domain' => 'kigali-tech',
                'business_type' => 'consulting',
                'email' => 'info@kigalitech.rw',
                'phone' => '+250788987654',
                'address' => 'KN 3 Rd, Kacyiru, Kigali, Rwanda',
                'subscription_plan' => 'professional',
                'subscription_expires_at' => now()->addMonths(6),
                'status' => 'active',
                'settings' => [
                    'currency' => 'RWF',
                    'timezone' => 'Africa/Kigali',
                    'language' => 'en',
                    'date_format' => 'd/m/Y',
                    'financial_year_start' => '01-07',
                ],
                'features' => [
                    'projects' => true,
                    'tasks' => true,
                    'finance' => true,
                    'reports' => true,
                    'team_management' => true,
                    'advanced_analytics' => false,
                ],
                'max_users' => 50,
                'trial_ends_at' => null,
                'admin_users' => [
                    [
                        'name' => 'Patrick Nkurunziza',
                        'email' => 'patrick@kigalitech.rw',
                        'role' => 'admin'
                    ]
                ]
            ],
            [
                'name' => 'East Africa Manufacturing',
                'domain' => 'ea-manufacturing',
                'business_type' => 'manufacturing',
                'email' => 'contact@eamanufacturing.com',
                'phone' => '+250788654321',
                'address' => 'Special Economic Zone, Kigali, Rwanda',
                'subscription_plan' => 'enterprise',
                'subscription_expires_at' => now()->addYear(),
                'status' => 'active',
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'Africa/Kigali',
                    'language' => 'en',
                    'date_format' => 'm/d/Y',
                    'financial_year_start' => '01-01',
                ],
                'features' => [
                    'projects' => true,
                    'tasks' => true,
                    'finance' => true,
                    'reports' => true,
                    'team_management' => true,
                    'advanced_analytics' => true,
                    'inventory_management' => true,
                ],
                'max_users' => 200,
                'trial_ends_at' => null,
                'admin_users' => [
                    [
                        'name' => 'Sarah Johnson',
                        'email' => 'sarah@eamanufacturing.com',
                        'role' => 'admin'
                    ],
                    [
                        'name' => 'David Niyonsenga',
                        'email' => 'david@eamanufacturing.com',
                        'role' => 'manager'
                    ]
                ]
            ],
            [
                'name' => 'Butare Retail Group',
                'domain' => 'butare-retail',
                'business_type' => 'retail',
                'email' => 'admin@butareretail.rw',
                'phone' => '+250788456789',
                'address' => 'Huye District, Southern Province, Rwanda',
                'subscription_plan' => 'professional',
                'subscription_expires_at' => now()->addMonths(3),
                'status' => 'active',
                'settings' => [
                    'currency' => 'RWF',
                    'timezone' => 'Africa/Kigali',
                    'language' => 'en',
                    'date_format' => 'Y-m-d',
                    'financial_year_start' => '01-01',
                ],
                'features' => [
                    'projects' => true,
                    'tasks' => true,
                    'finance' => true,
                    'reports' => true,
                    'team_management' => false,
                    'advanced_analytics' => false,
                ],
                'max_users' => 25,
                'trial_ends_at' => null,
                'admin_users' => [
                    [
                        'name' => 'Alice Uwera',
                        'email' => 'alice@butareretail.rw',
                        'role' => 'admin'
                    ]
                ]
            ],
            [
                'name' => 'Northern Transport Services',
                'domain' => 'northern-transport',
                'business_type' => 'service',
                'email' => 'dispatch@northerntransport.rw',
                'phone' => '+250788321654',
                'address' => 'Musanze District, Northern Province, Rwanda',
                'subscription_plan' => 'basic',
                'subscription_expires_at' => now()->addMonth(),
                'status' => 'active',
                'settings' => [
                    'currency' => 'RWF',
                    'timezone' => 'Africa/Kigali',
                    'language' => 'en',
                    'date_format' => 'd/m/Y',
                    'financial_year_start' => '01-01',
                ],
                'features' => [
                    'projects' => true,
                    'tasks' => true,
                    'finance' => false,
                    'reports' => false,
                    'team_management' => false,
                    'advanced_analytics' => false,
                ],
                'max_users' => 10,
                'trial_ends_at' => null,
                'admin_users' => [
                    [
                        'name' => 'Emmanuel Habimana',
                        'email' => 'emmanuel@northerntransport.rw',
                        'role' => 'admin'
                    ]
                ]
            ],
            [
                'name' => 'StartUp Incubator Rwanda',
                'domain' => 'startup-incubator',
                'business_type' => 'other',
                'email' => 'admin@startupincubator.rw',
                'phone' => '+250788159753',
                'address' => 'Norrsken House, KG 9 Ave, Kigali, Rwanda',
                'subscription_plan' => 'basic',
                'subscription_expires_at' => now()->addWeeks(2),
                'status' => 'active',
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'Africa/Kigali',
                    'language' => 'en',
                    'date_format' => 'Y-m-d',
                    'financial_year_start' => '01-01',
                ],
                'features' => [
                    'projects' => true,
                    'tasks' => true,
                    'finance' => false,
                    'reports' => false,
                    'team_management' => false,
                    'advanced_analytics' => false,
                ],
                'max_users' => 15,
                'trial_ends_at' => now()->addDays(5), // Still in trial
                'admin_users' => [
                    [
                        'name' => 'Grace Uwimana',
                        'email' => 'grace@startupincubator.rw',
                        'role' => 'admin'
                    ]
                ]
            ],
            [
                'name' => 'Kamonyi Agricultural Coop',
                'domain' => 'kamonyi-agri',
                'business_type' => 'other',
                'email' => 'coop@kamonyiagri.rw',
                'phone' => '+250788753951',
                'address' => 'Kamonyi District, Southern Province, Rwanda',
                'subscription_plan' => 'professional',
                'subscription_expires_at' => now()->addMonths(8),
                'status' => 'suspended', // Suspended for testing
                'settings' => [
                    'currency' => 'RWF',
                    'timezone' => 'Africa/Kigali',
                    'language' => 'rw',
                    'date_format' => 'd/m/Y',
                    'financial_year_start' => '01-07',
                ],
                'features' => [
                    'projects' => true,
                    'tasks' => true,
                    'finance' => true,
                    'reports' => true,
                    'team_management' => true,
                    'advanced_analytics' => false,
                ],
                'max_users' => 30,
                'trial_ends_at' => null,
                'admin_users' => [
                    [
                        'name' => 'Innocent Nsengimana',
                        'email' => 'innocent@kamonyiagri.rw',
                        'role' => 'admin'
                    ]
                ]
            ]
        ];

        foreach ($tenants as $tenantData) {
            $this->command->info("Creating tenant: {$tenantData['name']}");

            // Extract admin users before creating tenant
            $adminUsers = $tenantData['admin_users'];
            unset($tenantData['admin_users']);

            // Generate database name
            $tenantData['database'] = 'tenant_' . $tenantData['domain'];

            // Create the tenant only if it doesn't exist
            $tenant = Tenant::where('domain', $tenantData['domain'])->first();
            
            if (!$tenant) {
                $this->command->info("Creating tenant: {$tenantData['name']}");
                $tenant = Tenant::create($tenantData);
            } else {
                $this->command->info("Tenant already exists: {$tenantData['name']}");
            }

            // Create admin users for this tenant
            foreach ($adminUsers as $userData) {
                // Check if user already exists
                $user = User::where('email', $userData['email'])->first();
                
                if (!$user) {
                    // Create new user
                    $user = User::create([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => bcrypt('password123'), // Default password
                        'email_verified_at' => now(),
                        'is_super_admin' => false,
                    ]);
                    
                    $this->command->info("  → Created user: {$userData['name']} ({$userData['email']})");
                }

                // Add user to tenant with specified role
                $user->addToTenant(
                    $tenant->id, 
                    $userData['role'], 
                    $userData['role'] === 'admin'
                );

                $this->command->info("  → Added {$userData['name']} as {$userData['role']} to {$tenant->name}");
            }

            $this->command->info("✅ Tenant '{$tenant->name}' created successfully");
        }

        // Update the super admin to have access to all tenants
        $superAdmin = User::where('is_super_admin', true)->first();
        if ($superAdmin) {
            $allTenants = Tenant::all();
            foreach ($allTenants as $tenant) {
                if (!$superAdmin->belongsToTenant($tenant->id)) {
                    $superAdmin->addToTenant($tenant->id, 'super_admin', true);
                }
            }
            $this->command->info("✅ Super admin granted access to all tenants");
        }

        $this->command->info('🎉 Sample tenants seeding completed!');
        $this->command->info('📊 Summary:');
        $this->command->info('  • Total tenants created: ' . count($tenants));
        $this->command->info('  • Active tenants: ' . Tenant::where('status', 'active')->count());
        $this->command->info('  • Suspended tenants: ' . Tenant::where('status', 'suspended')->count());
        $this->command->info('  • Enterprise plans: ' . Tenant::where('subscription_plan', 'enterprise')->count());
        $this->command->info('  • Professional plans: ' . Tenant::where('subscription_plan', 'professional')->count());
        $this->command->info('  • Basic plans: ' . Tenant::where('subscription_plan', 'basic')->count());
        $this->command->info('');
        $this->command->info('🔑 Default password for all users: password123');
        $this->command->info('🌐 Access tenant management at: /admin/tenants');
        $this->command->info('📈 View analytics at: /admin/analytics');
    }
}