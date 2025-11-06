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
        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('plan'); // basic, professional, enterprise
            $table->enum('status', ['active', 'cancelled', 'past_due', 'paused'])->default('active');
            $table->decimal('amount', 10, 2); // Monthly/yearly amount
            $table->string('currency', 3)->default('USD');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('trial_start')->nullable();
            $table->timestamp('trial_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->json('features')->nullable(); // Enabled features for this plan
            $table->json('usage_limits')->nullable(); // Usage limits (users, projects, etc.)
            $table->json('usage_current')->nullable(); // Current usage metrics
            $table->string('external_subscription_id')->nullable(); // For payment providers
            $table->string('payment_method')->nullable(); // card, bank_transfer, etc.
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['status']);
            $table->index(['current_period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
    }
};
