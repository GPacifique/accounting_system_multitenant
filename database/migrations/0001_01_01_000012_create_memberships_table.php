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
        // Only create the table if it doesn't exist
        if (!Schema::hasTable('memberships')) {
            Schema::create('memberships', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->foreignId('member_id')->constrained()->onDelete('cascade');
                $table->enum('membership_type', [
                    'basic', 'premium', 'vip', 'student', 'senior', 'family', 'corporate', 'day_pass'
                ]);
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->decimal('price', 10, 2);
                $table->enum('status', ['active', 'expired', 'suspended', 'cancelled', 'pending'])->default('active');
                $table->enum('payment_frequency', [
                    'monthly', 'quarterly', 'semi_annual', 'annual', 'one_time'
                ])->default('monthly');
                $table->boolean('auto_renewal')->default(false);
                $table->json('benefits')->nullable(); // List of benefits included
                $table->json('restrictions')->nullable(); // Any restrictions or limitations
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->text('notes')->nullable();
                $table->timestamps();

                // Indexes for performance
                $table->index(['tenant_id', 'member_id']);
                $table->index(['tenant_id', 'membership_type']);
                $table->index(['tenant_id', 'status']);
                $table->index(['start_date', 'end_date']);
                $table->index('end_date');
                $table->index('auto_renewal');
                $table->index('payment_frequency');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};