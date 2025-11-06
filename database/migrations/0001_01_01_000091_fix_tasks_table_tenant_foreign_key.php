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
        Schema::table('tasks', function (Blueprint $table) {
            // Add foreign key constraint for tenant_id if it doesn't exist
            if (Schema::hasColumn('tasks', 'tenant_id')) {
                try {
                    // First check if the constraint already exists
                    $foreignKeys = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'tasks' 
                        AND COLUMN_NAME = 'tenant_id'
                        AND REFERENCED_TABLE_NAME = 'tenants'
                    ");
                    
                    if (empty($foreignKeys)) {
                        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                        echo "✅ Added foreign key constraint for tasks.tenant_id\n";
                    } else {
                        echo "ℹ️ Foreign key constraint for tasks.tenant_id already exists\n";
                    }
                } catch (Exception $e) {
                    echo "⚠️ Could not add foreign key constraint: " . $e->getMessage() . "\n";
                }
            } else {
                echo "❌ tenant_id column not found in tasks table\n";
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            try {
                $table->dropForeign(['tenant_id']);
                echo "✅ Dropped foreign key constraint for tasks.tenant_id\n";
            } catch (Exception $e) {
                echo "⚠️ Could not drop foreign key constraint: " . $e->getMessage() . "\n";
            }
        });
    }
};
