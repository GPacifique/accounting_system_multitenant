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
        if (!Schema::hasTable('fitness_classes')) {
            Schema::create('fitness_classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->foreignId('trainer_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('class_type', [
                    'yoga', 'pilates', 'zumba', 'spinning', 'crossfit', 'aerobics', 
                    'strength_training', 'hiit', 'martial_arts', 'swimming'
                ]);
                $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('all_levels');
                $table->integer('duration_minutes');
                $table->integer('max_capacity');
                $table->integer('current_capacity')->default(0);
                $table->decimal('price_per_session', 8, 2)->default(0);
                $table->date('class_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('location')->nullable();
                $table->json('equipment_needed')->nullable();
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed'])->default('scheduled');
                $table->text('notes')->nullable();
                $table->boolean('is_recurring')->default(false);
                $table->json('recurring_pattern')->nullable(); // For recurring classes
                $table->timestamps();

                // Indexes for performance
                $table->index(['tenant_id', 'class_date']);
                $table->index(['tenant_id', 'class_type']);
                $table->index(['tenant_id', 'trainer_id']);
                $table->index(['tenant_id', 'status']);
                $table->index(['class_date', 'start_time']);
                $table->index('difficulty_level');

                // Unique constraints for preventing double booking
                $table->unique(['tenant_id', 'trainer_id', 'class_date', 'start_time'], 'unique_trainer_schedule');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fitness_classes');
    }
};