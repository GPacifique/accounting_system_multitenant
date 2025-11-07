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
        if (!Schema::hasTable('class_bookings')) {
            Schema::create('class_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->foreignId('member_id')->constrained()->onDelete('cascade');
                $table->foreignId('fitness_class_id')->constrained()->onDelete('cascade');
                $table->timestamp('booking_date');
                $table->enum('status', ['confirmed', 'pending', 'cancelled', 'no_show', 'waitlist'])->default('confirmed');
                $table->decimal('amount_paid', 8, 2)->default(0);
                $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'membership', 'free'])->default('membership');
                $table->text('notes')->nullable();
                $table->enum('attendance_status', ['attended', 'no_show', 'late', 'pending'])->default('pending');
                $table->timestamp('checked_in_at')->nullable();
                $table->timestamps();

                // Indexes for performance
                $table->index(['tenant_id', 'member_id']);
                $table->index(['tenant_id', 'fitness_class_id']);
                $table->index(['tenant_id', 'booking_date']);
                $table->index(['tenant_id', 'status']);
                $table->index('attendance_status');
                $table->index('checked_in_at');

                // Prevent duplicate bookings
                $table->unique(['member_id', 'fitness_class_id'], 'unique_member_class_booking');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_bookings');
    }
};