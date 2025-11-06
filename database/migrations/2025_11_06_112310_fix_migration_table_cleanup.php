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
        // Fix the migration table by removing the old migration record that was renamed
        $oldMigrationName = '2025_11_05_095510_add_tenant_id_to_business_tables';
        $newMigrationName = '2025_11_05_164000_add_tenant_id_to_business_tables';
        
        // Check if the old migration record exists in the migrations table
        $oldMigrationExists = DB::table('migrations')
            ->where('migration', $oldMigrationName)
            ->exists();
            
        $newMigrationExists = DB::table('migrations')
            ->where('migration', $newMigrationName)
            ->exists();
        
        if ($oldMigrationExists) {
            // If the old migration exists, delete it
            DB::table('migrations')
                ->where('migration', $oldMigrationName)
                ->delete();
                
            echo "Removed old migration record: {$oldMigrationName}\n";
        }
        
        // If the new migration doesn't exist in migrations table, we'll let it run normally
        if (!$newMigrationExists) {
            echo "New migration {$newMigrationName} will run normally.\n";
        } else {
            echo "New migration {$newMigrationName} already exists in migrations table.\n";
        }
        
        echo "Migration table cleanup completed successfully.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to rollback for this cleanup migration
        echo "Migration table cleanup rollback - no action needed.\n";
    }
};
