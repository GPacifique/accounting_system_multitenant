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
        if (!Schema::hasTable('gym_revenues')) {
            Schema::create('gym_revenues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
                $table->string('revenue_type'); // membership, class, personal_training, equipment_rental, etc.
                $table->string('description');
                $table->decimal('amount', 15, 2);
                $table->date('received_at');
                $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
                $table->foreignId('trainer_id')->nullable()->constrained('trainers')->onDelete('set null');
                $table->foreignId('fitness_class_id')->nullable()->constrained('fitness_classes')->onDelete('set null');
                $table->string('payment_method')->nullable(); // cash, card, bank_transfer
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['tenant_id', 'revenue_type']);
                $table->index(['tenant_id', 'received_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_revenues');
    }
};