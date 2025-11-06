<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Make tenant_id NOT NULL for all business tables to ensure data integrity.
     * This prevents orphaned records and enforces proper tenant isolation.
     */
    public function up(): void
    {
        echo "🔧 Making tenant_id NOT NULL for business tables...\n";
        
        // Business tables that should have NOT NULL tenant_id
        $businessTables = [
            'clients', 'projects', 'incomes', 'expenses', 'employees', 
            'payments', 'workers', 'worker_payments', 'tasks', 'orders', 
            'order_items', 'products', 'transactions', 'reports', 'settings'
        ];
        
        foreach ($businessTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                try {
                    // First, update any NULL tenant_id values to a default tenant
                    // (This should not happen in a properly configured system)
                    $nullCount = DB::table($tableName)->whereNull('tenant_id')->count();
                    if ($nullCount > 0) {
                        echo "⚠️ Found {$nullCount} NULL tenant_id records in {$tableName}\n";
                        
                        // Get the first available tenant or create a default one
                        $defaultTenant = DB::table('tenants')->first();
                        if ($defaultTenant) {
                            DB::table($tableName)
                                ->whereNull('tenant_id')
                                ->update(['tenant_id' => $defaultTenant->id]);
                            echo "✅ Updated NULL tenant_id records in {$tableName} to tenant {$defaultTenant->id}\n";
                        } else {
                            echo "❌ No tenants found! Cannot update NULL tenant_id records in {$tableName}\n";
                            continue;
                        }
                    }
                    
                    // Check if tenant_id is already NOT NULL
                    $columns = DB::select("DESCRIBE {$tableName}");
                    $tenantIdColumn = collect($columns)->firstWhere('Field', 'tenant_id');
                    
                    if ($tenantIdColumn && $tenantIdColumn->Null === 'YES') {
                        // Make tenant_id NOT NULL
                        Schema::table($tableName, function (Blueprint $table) {
                            $table->bigInteger('tenant_id')->unsigned()->nullable(false)->change();
                        });
                        echo "✅ Made tenant_id NOT NULL in {$tableName}\n";
                    } else {
                        echo "ℹ️ tenant_id already NOT NULL in {$tableName}\n";
                    }
                    
                } catch (Exception $e) {
                    echo "⚠️ Could not update {$tableName}: " . $e->getMessage() . "\n";
                }
            } else {
                echo "➖ {$tableName} - Table or tenant_id column not found\n";
            }
        }
        
        echo "✅ Completed making tenant_id NOT NULL for business tables\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "⚠️ Reverting tenant_id to nullable for business tables...\n";
        
        $businessTables = [
            'clients', 'projects', 'incomes', 'expenses', 'employees', 
            'payments', 'workers', 'worker_payments', 'tasks', 'orders', 
            'order_items', 'products', 'transactions', 'reports', 'settings'
        ];
        
        foreach ($businessTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->bigInteger('tenant_id')->unsigned()->nullable()->change();
                    });
                    echo "✅ Made tenant_id nullable in {$tableName}\n";
                } catch (Exception $e) {
                    echo "⚠️ Could not revert {$tableName}: " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "✅ Completed reverting tenant_id to nullable\n";
    }
};
