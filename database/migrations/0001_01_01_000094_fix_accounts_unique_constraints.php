<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix accounts table unique constraints:
     * - Remove global code unique constraint (wrong for multi-tenant)
     * - Keep tenant-scoped code unique constraint (correct for multi-tenant)
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Drop the global code unique constraint
            try {
                $table->dropUnique(['code']);
                echo "✅ Dropped global code unique constraint\n";
            } catch (Exception $e) {
                echo "ℹ️ Global code unique constraint not found or already dropped\n";
            }
            
            // Ensure tenant-scoped unique constraint exists
            try {
                $table->unique(['tenant_id', 'code'], 'accounts_tenant_code_unique');
                echo "✅ Added tenant-scoped code unique constraint\n";
            } catch (Exception $e) {
                echo "ℹ️ Tenant-scoped code unique constraint already exists\n";
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // This rollback is not recommended as it would break multi-tenant isolation
            // But included for completeness
            try {
                $table->dropUnique('accounts_tenant_code_unique');
                $table->unique('code');
                echo "⚠️ Reverted to global code unique constraint (breaks multi-tenant)\n";
            } catch (Exception $e) {
                echo "⚠️ Could not revert constraints\n";
            }
        });
    }
};
