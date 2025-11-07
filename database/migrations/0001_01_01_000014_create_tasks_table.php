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
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->nullable()->constrained()->onDelete('cascade'); // For equipment maintenance
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('task_type', [
                'maintenance', 'cleaning', 'equipment_check', 'member_follow_up', 
                'inventory', 'safety_inspection', 'administrative', 'marketing', 'other'
            ])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            
            $table->date('due_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('completed_date')->nullable();
            
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();
            
            $table->json('attachments')->nullable(); // Store file paths
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id']);
            $table->index(['equipment_id']);
            $table->index(['assigned_to']);
            $table->index(['task_type']);
            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['due_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
