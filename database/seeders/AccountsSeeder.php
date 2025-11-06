<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Create a comprehensive chart of accounts for each tenant
     */
    public function run(): void
    {
        $this->command->info('🏦 Creating Chart of Accounts...');
        
        // Get all tenants
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->command->warn('⚠️  No tenants found. Creating a default tenant first...');
            $defaultUser = User::first();
            $defaultTenant = Tenant::create([
                'name' => 'Default Company',
                'domain' => 'default',
                'contact_email' => 'admin@default.com',
                'created_by' => $defaultUser->id ?? 1,
                'status' => 'active'
            ]);
            $tenants = collect([$defaultTenant]);
        }
        
        foreach ($tenants as $tenant) {
            $this->command->info("📊 Creating accounts for tenant: {$tenant->name}");
            
            // Check if accounts already exist for this tenant
            $existingAccounts = Account::where('tenant_id', $tenant->id)->count();
            if ($existingAccounts > 0) {
                $this->command->warn("   ⚠️  Tenant {$tenant->name} already has {$existingAccounts} accounts. Skipping...");
                continue;
            }
            
            $this->createChartOfAccounts($tenant);
        }
        
        $this->command->info('✅ Chart of Accounts created successfully!');
    }
    
    /**
     * Create a comprehensive chart of accounts for a tenant
     */
    private function createChartOfAccounts(Tenant $tenant): void
    {
        $defaultUser = User::first();
        
        $accounts = [
            // ASSETS (1000-1999)
            [
                'code' => '1000',
                'name' => 'Current Assets',
                'type' => 'asset',
                'parent_id' => null,
                'description' => 'Short-term assets',
                'opening_balance' => 0,
                'is_system' => true,
            ],
            [
                'code' => '1001',
                'name' => 'Cash',
                'type' => 'asset',
                'parent_code' => '1000',
                'description' => 'Cash on hand and in bank',
                'opening_balance' => 50000.00,
                'tax_rate' => 0.00,
            ],
            [
                'code' => '1002',
                'name' => 'Checking Account',
                'type' => 'asset',
                'parent_code' => '1000',
                'description' => 'Primary business checking account',
                'opening_balance' => 100000.00,
                'tax_rate' => 0.00,
            ],
            [
                'code' => '1003',
                'name' => 'Savings Account',
                'type' => 'asset',
                'parent_code' => '1000',
                'description' => 'Business savings account',
                'opening_balance' => 25000.00,
                'tax_rate' => 0.00,
            ],
            [
                'code' => '1100',
                'name' => 'Accounts Receivable',
                'type' => 'asset',
                'parent_code' => '1000',
                'description' => 'Money owed by customers',
                'opening_balance' => 75000.00,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '1200',
                'name' => 'Inventory',
                'type' => 'asset',
                'parent_code' => '1000',
                'description' => 'Products and materials',
                'opening_balance' => 30000.00,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '1500',
                'name' => 'Fixed Assets',
                'type' => 'asset',
                'parent_id' => null,
                'description' => 'Long-term assets',
                'opening_balance' => 0,
                'is_system' => true,
            ],
            [
                'code' => '1501',
                'name' => 'Equipment',
                'type' => 'asset',
                'parent_code' => '1500',
                'description' => 'Construction equipment',
                'opening_balance' => 200000.00,
                'tax_rate' => 0.00,
            ],
            [
                'code' => '1502',
                'name' => 'Vehicles',
                'type' => 'asset',
                'parent_code' => '1500',
                'description' => 'Company vehicles',
                'opening_balance' => 150000.00,
                'tax_rate' => 0.00,
            ],
            
            // LIABILITIES (2000-2999)
            [
                'code' => '2000',
                'name' => 'Current Liabilities',
                'type' => 'liability',
                'parent_id' => null,
                'description' => 'Short-term obligations',
                'opening_balance' => 0,
                'is_system' => true,
            ],
            [
                'code' => '2001',
                'name' => 'Accounts Payable',
                'type' => 'liability',
                'parent_code' => '2000',
                'description' => 'Money owed to suppliers',
                'opening_balance' => 25000.00,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '2100',
                'name' => 'Taxes Payable',
                'type' => 'liability',
                'parent_code' => '2000',
                'description' => 'Tax obligations',
                'opening_balance' => 15000.00,
                'tax_rate' => 0.00,
            ],
            [
                'code' => '2200',
                'name' => 'Accrued Expenses',
                'type' => 'liability',
                'parent_code' => '2000',
                'description' => 'Unpaid expenses',
                'opening_balance' => 10000.00,
                'tax_rate' => 18.00,
            ],
            
            // EQUITY (3000-3999)
            [
                'code' => '3000',
                'name' => 'Owner\'s Equity',
                'type' => 'equity',
                'parent_id' => null,
                'description' => 'Owner\'s investment and retained earnings',
                'opening_balance' => 500000.00,
                'tax_rate' => 0.00,
            ],
            [
                'code' => '3100',
                'name' => 'Retained Earnings',
                'type' => 'equity',
                'parent_code' => '3000',
                'description' => 'Accumulated profits',
                'opening_balance' => 100000.00,
                'tax_rate' => 0.00,
            ],
            
            // REVENUE (4000-4999)
            [
                'code' => '4000',
                'name' => 'Construction Revenue',
                'type' => 'revenue',
                'parent_id' => null,
                'description' => 'Income from construction projects',
                'opening_balance' => 0,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '4100',
                'name' => 'Service Revenue',
                'type' => 'revenue',
                'parent_id' => null,
                'description' => 'Income from services',
                'opening_balance' => 0,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '4200',
                'name' => 'Other Income',
                'type' => 'revenue',
                'parent_id' => null,
                'description' => 'Miscellaneous income',
                'opening_balance' => 0,
                'tax_rate' => 18.00,
            ],
            
            // EXPENSES (5000-5999)
            [
                'code' => '5000',
                'name' => 'Cost of Goods Sold',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Direct costs of construction',
                'opening_balance' => 0,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '5100',
                'name' => 'Materials',
                'type' => 'expense',
                'parent_code' => '5000',
                'description' => 'Construction materials',
                'opening_balance' => 0,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '5200',
                'name' => 'Labor',
                'type' => 'expense',
                'parent_code' => '5000',
                'description' => 'Direct labor costs',
                'opening_balance' => 0,
                'tax_rate' => 0.00,
            ],
            [
                'code' => '6000',
                'name' => 'Operating Expenses',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Business operating expenses',
                'opening_balance' => 0,
                'is_system' => true,
            ],
            [
                'code' => '6100',
                'name' => 'Office Rent',
                'type' => 'expense',
                'parent_code' => '6000',
                'description' => 'Office rental expenses',
                'opening_balance' => 0,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '6200',
                'name' => 'Utilities',
                'type' => 'expense',
                'parent_code' => '6000',
                'description' => 'Electricity, water, internet',
                'opening_balance' => 0,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '6300',
                'name' => 'Equipment Maintenance',
                'type' => 'expense',
                'parent_code' => '6000',
                'description' => 'Equipment repairs and maintenance',
                'opening_balance' => 0,
                'tax_rate' => 18.00,
            ],
            [
                'code' => '6400',
                'name' => 'Fuel & Transportation',
                'type' => 'expense',
                'parent_code' => '6000',
                'description' => 'Vehicle fuel and transport costs',
                'opening_balance' => 0,
                'tax_rate' => 18.00,
            ],
        ];
        
        // Create accounts with parent-child relationships
        $accountMap = [];
        
        foreach ($accounts as $accountData) {
            $parentId = null;
            
            // Resolve parent_id from parent_code
            if (isset($accountData['parent_code'])) {
                $parentCode = $accountData['parent_code'];
                if (isset($accountMap[$parentCode])) {
                    $parentId = $accountMap[$parentCode];
                }
                unset($accountData['parent_code']);
            }
            
            $account = Account::create([
                'tenant_id' => $tenant->id,
                'code' => $accountData['code'],
                'name' => $accountData['name'],
                'type' => $accountData['type'],
                'parent_id' => $parentId,
                'description' => $accountData['description'] ?? null,
                'opening_balance' => $accountData['opening_balance'] ?? 0,
                'current_balance' => $accountData['opening_balance'] ?? 0,
                'currency' => 'RWF',
                'tax_rate' => $accountData['tax_rate'] ?? 0.00,
                'is_system' => $accountData['is_system'] ?? false,
                'is_active' => true,
                'created_by' => $defaultUser->id ?? 1,
                'updated_by' => $defaultUser->id ?? 1,
            ]);
            
            $accountMap[$accountData['code']] = $account->id;
        }
        
        $this->command->info("   ✅ Created " . count($accounts) . " accounts for {$tenant->name}");
    }
}
