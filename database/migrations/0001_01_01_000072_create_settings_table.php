<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Run the migrations.
         */
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
            
            // Add indexes for performance
            $table->index(['tenant_id']);
            $table->index(['tenant_id', 'key']);
            $table->unique(['tenant_id', 'key'], 'settings_tenant_key_unique');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
