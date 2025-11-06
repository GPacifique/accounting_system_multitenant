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
        // First, check if the incomes table exists
        if (!Schema::hasTable('incomes')) {
            // Create the incomes table if it doesn't exist
            Schema::create('incomes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');  
                $table->string('invoice_number')->unique();
                $table->decimal('amount_received', 15, 2);
                $table->enum('payment_status', ['Paid', 'Pending','partially paid','Overdue'])->default('Pending');
                $table->decimal('amount_remaining', 15, 2)->default(0);
                $table->date('received_at');        
                $table->text('notes')->nullable(); 
                $table->timestamps();
                
                $table->index(['tenant_id']);
            });
        } else {
            // If table exists, ensure it has the correct columns
            Schema::table('incomes', function (Blueprint $table) {
                // Check if amount_received column exists, if not add it
                if (!Schema::hasColumn('incomes', 'amount_received')) {
                    $table->decimal('amount_received', 15, 2)->after('invoice_number');
                }
                
                // Ensure the table has tenant_id if it doesn't
                if (!Schema::hasColumn('incomes', 'tenant_id')) {
                    $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                    $table->index(['tenant_id']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it
        if (Schema::hasTable('incomes') && Schema::hasColumn('incomes', 'amount_received')) {
            Schema::table('incomes', function (Blueprint $table) {
                if (Schema::hasColumn('incomes', 'amount_received')) {
                    $table->dropColumn('amount_received');
                }
            });
        }
    }
};
