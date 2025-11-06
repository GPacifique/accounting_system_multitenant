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
        // Check if projects table exists and add missing columns
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                // Add missing columns if they don't exist
                if (!Schema::hasColumn('projects', 'contract_value')) {
                    $table->decimal('contract_value', 14, 2)->default(0)->after('end_date');
                }
                if (!Schema::hasColumn('projects', 'amount_paid')) {
                    $table->decimal('amount_paid', 14, 2)->default(0)->after('contract_value');
                }
                if (!Schema::hasColumn('projects', 'amount_remaining')) {
                    $table->decimal('amount_remaining', 14, 2)->default(0)->after('amount_paid');
                }
                if (!Schema::hasColumn('projects', 'status')) {
                    $table->string('status')->nullable()->after('amount_remaining');
                }
                if (!Schema::hasColumn('projects', 'notes')) {
                    $table->text('notes')->nullable()->after('status');
                }
                if (!Schema::hasColumn('projects', 'client_id')) {
                    $table->foreignId('client_id')->nullable()->after('tenant_id')->constrained('clients')->onDelete('cascade');
                }
                if (!Schema::hasColumn('projects', 'start_date')) {
                    $table->date('start_date')->nullable()->after('name');
                }
                if (!Schema::hasColumn('projects', 'end_date')) {
                    $table->date('end_date')->nullable()->after('start_date');
                }
            });
        } else {
            // Create projects table if it doesn't exist
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
                $table->string('name');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->decimal('contract_value', 14, 2)->default(0);
                $table->decimal('amount_paid', 14, 2)->default(0);
                $table->decimal('amount_remaining', 14, 2)->default(0);
                $table->string('status')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['tenant_id']);
                $table->index(['client_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                $columns = ['contract_value', 'amount_paid', 'amount_remaining', 'status', 'notes', 'client_id', 'start_date', 'end_date'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('projects', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
