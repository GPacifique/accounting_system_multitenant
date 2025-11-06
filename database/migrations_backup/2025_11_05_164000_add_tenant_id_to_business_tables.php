<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Business tables that need tenant_id for data isolation
        $businessTables = [
            'clients',
            'projects', 
            'incomes',
            'expenses',
            'employees',
            'payments',
            'workers',
            'worker_payments',
            'tasks',
            'orders',
            'order_items',
            'products',
            'transactions',
            'reports',
            'settings',
        ];

        foreach ($businessTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                // Check if tenant_id column already exists
                if (!Schema::hasColumn($tableName, 'tenant_id')) {
                    try {
                        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                            $table->index(['tenant_id']);
                        });
                        
                        echo "Added tenant_id to {$tableName} table successfully.\n";
                    } catch (Exception $e) {
                        echo "Failed to add tenant_id to {$tableName}: " . $e->getMessage() . "\n";
                        // Continue with other tables instead of failing completely
                    }
                } else {
                    echo "tenant_id already exists in {$tableName} table.\n";
                }
            } else {
                echo "Table {$tableName} does not exist, skipping.\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $businessTables = [
            'clients',
            'projects', 
            'incomes',
            'expenses',
            'employees',
            'payments',
            'workers',
            'worker_payments',
            'tasks',
            'orders',
            'order_items',
            'products',
            'transactions',
            'reports',
            'settings',
        ];

        foreach ($businessTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropForeign(['tenant_id']);
                        $table->dropColumn('tenant_id');
                    });
                    echo "Removed tenant_id from {$tableName} table successfully.\n";
                } catch (Exception $e) {
                    echo "Failed to remove tenant_id from {$tableName}: " . $e->getMessage() . "\n";
                }
            }
        }
    }
};
