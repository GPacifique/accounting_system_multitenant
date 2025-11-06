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
        Schema::table('tenants', function (Blueprint $table) {
            // Add missing tenant management columns only if they don't exist
            if (!Schema::hasColumn('tenants', 'features')) {
                $table->json('features')->nullable()->after('settings');
            }
            if (!Schema::hasColumn('tenants', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('email');
            }
            if (!Schema::hasColumn('tenants', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('tenants', 'description')) {
                $table->text('description')->nullable()->after('address');
            }
            if (!Schema::hasColumn('tenants', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('description');
            }
            if (!Schema::hasColumn('tenants', 'timezone')) {
                $table->string('timezone')->default('Africa/Kigali')->after('logo_path');
            }
            if (!Schema::hasColumn('tenants', 'currency')) {
                $table->string('currency', 3)->default('RWF')->after('timezone');
            }
            if (!Schema::hasColumn('tenants', 'locale')) {
                $table->string('locale', 5)->default('en')->after('currency');
            }
            if (!Schema::hasColumn('tenants', 'max_users')) {
                $table->integer('max_users')->default(10)->after('locale');
            }
            if (!Schema::hasColumn('tenants', 'max_concurrent_sessions')) {
                $table->integer('max_concurrent_sessions')->default(3)->after('max_users');
            }
            if (!Schema::hasColumn('tenants', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable()->after('subscription_expires_at');
            }
            if (!Schema::hasColumn('tenants', 'enforce_2fa')) {
                $table->boolean('enforce_2fa')->default(false)->after('trial_ends_at');
            }
            if (!Schema::hasColumn('tenants', 'session_timeout')) {
                $table->integer('session_timeout')->default(120)->after('enforce_2fa'); // minutes
            }
            if (!Schema::hasColumn('tenants', 'last_backup_at')) {
                $table->timestamp('last_backup_at')->nullable()->after('session_timeout');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'features',
                'contact_email',
                'contact_phone',
                'description',
                'logo_path',
                'timezone',
                'currency',
                'locale',
                'max_users',
                'max_concurrent_sessions',
                'trial_ends_at',
                'enforce_2fa',
                'session_timeout',
                'last_backup_at'
            ]);
        });
    }
};
