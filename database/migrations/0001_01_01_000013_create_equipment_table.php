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
        if (!Schema::hasTable('equipment')) {
            Schema::create('equipment', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('brand')->nullable();
                $table->string('model')->nullable();
                $table->string('serial_number')->nullable();
                $table->enum('equipment_type', [
                    'cardio', 'strength', 'free_weights', 'functional', 'accessories', 
                    'pool', 'safety', 'cleaning', 'audio_visual', 'other'
                ]);
                $table->text('description')->nullable();
                $table->string('location'); // e.g., 'Main Floor', 'Cardio Area', 'Weight Room'
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_price', 10, 2)->nullable();
                $table->date('warranty_expiry')->nullable();
                $table->enum('status', ['operational', 'maintenance', 'out_of_order', 'retired'])->default('operational');
                $table->text('maintenance_notes')->nullable();
                $table->date('last_maintenance_date')->nullable();
                $table->date('next_maintenance_due')->nullable();
                $table->integer('usage_hours')->default(0); // Track usage for maintenance scheduling
                $table->json('maintenance_schedule')->nullable(); // Recurring maintenance tasks
                $table->string('manufacturer_contact')->nullable();
                $table->text('safety_instructions')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();

                // Indexes for performance
                $table->index(['tenant_id', 'equipment_type']);
                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'location']);
                $table->index('next_maintenance_due');
                $table->index('warranty_expiry');
                $table->index('serial_number');

                // Unique constraints
                $table->unique(['tenant_id', 'serial_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};