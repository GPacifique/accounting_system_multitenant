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
        if (!Schema::hasTable('tenants')) {
            Schema::create('tenants', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('domain')->unique();
                $table->string('database')->nullable();
                $table->string('business_type')->default('other');
                $table->string('email')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->text('description')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('timezone')->default('Africa/Kigali');
                $table->string('currency', 3)->default('RWF');
                $table->string('locale', 5)->default('en');
                $table->integer('max_users')->default(10);
                $table->integer('max_concurrent_sessions')->default(3);
                $table->timestamp('trial_ends_at')->nullable();
                $table->json('features')->nullable();
                $table->boolean('enforce_2fa')->default(false);
                $table->integer('session_timeout')->default(120); // minutes
                $table->timestamp('last_backup_at')->nullable();
                $table->json('settings')->nullable();
                $table->enum('status', ['active', 'suspended', 'inactive'])->default('active');
                $table->enum('subscription_plan', ['basic', 'professional', 'enterprise'])->default('basic');
                $table->timestamp('subscription_expires_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                // Indexes for performance
                $table->index(['status']);
                $table->index(['business_type']);
                $table->index(['subscription_plan']);
                $table->index(['domain']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
