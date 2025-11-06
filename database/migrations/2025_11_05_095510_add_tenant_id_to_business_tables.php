<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Only add tenant_id if it doesn't already exist
                    if (!Schema::hasColumn($tableName, 'tenant_id')) {
                        $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                        $table->index(['tenant_id']);
                    }
                });
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
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
