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
        if (!Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->nullable();
                $table->string('phone');
                $table->text('address')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_phone')->nullable();
                $table->enum('membership_type', ['basic', 'premium', 'vip', 'student', 'day_pass'])->default('basic');
                $table->date('membership_start_date')->nullable();
                $table->date('membership_end_date')->nullable();
                $table->enum('membership_status', ['active', 'expired', 'suspended', 'cancelled'])->default('active');
                $table->json('medical_conditions')->nullable();
                $table->json('fitness_goals')->nullable();
                $table->timestamp('joined_at')->nullable();
                $table->timestamp('last_visit_at')->nullable();
                $table->timestamps();

                // Indexes for performance
                $table->index(['tenant_id', 'email']);
                $table->index(['tenant_id', 'membership_status']);
                $table->index(['tenant_id', 'membership_type']);
                $table->index('membership_end_date');
                $table->index('last_visit_at');

                // Unique constraints for tenant-scoped data
                $table->unique(['tenant_id', 'email']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};