<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix tax_rate column to allow reasonable tax rate values (0-100%)
     * Current: decimal(5,4) - max 9.9999
     * New: decimal(6,4) - max 99.9999
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Change tax_rate column to allow values up to 99.9999%
            $table->decimal('tax_rate', 6, 4)->nullable()->change();
        });
        
        echo "✅ Fixed accounts.tax_rate column to allow values up to 99.9999%\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Revert back to original size (not recommended)
            $table->decimal('tax_rate', 5, 4)->nullable()->change();
        });
        
        echo "⚠️ Reverted accounts.tax_rate column to original size (max 9.9999%)\n";
    }
};
