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
        if (!Schema::hasTable('trainers')) {
            Schema::create('trainers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->string('phone');
                $table->json('specializations')->nullable(); // e.g., ['personal_training', 'yoga', 'pilates']
                $table->json('certifications')->nullable(); // e.g., ['ACSM', 'NASM', 'RYT-200']
                $table->date('hire_date');
                $table->decimal('hourly_rate', 8, 2)->default(0);
                $table->decimal('commission_rate', 5, 2)->default(0); // Percentage
                $table->enum('status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active');
                $table->text('bio')->nullable();
                $table->integer('experience_years')->default(0);
                $table->json('languages_spoken')->nullable(); // e.g., ['english', 'spanish', 'french']
                $table->json('availability_schedule')->nullable(); // Weekly schedule
                $table->string('profile_image')->nullable();
                $table->timestamps();

                // Indexes for performance
                $table->index(['tenant_id', 'status']);
                $table->index('hire_date');

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
        Schema::dropIfExists('trainers');
    }
};