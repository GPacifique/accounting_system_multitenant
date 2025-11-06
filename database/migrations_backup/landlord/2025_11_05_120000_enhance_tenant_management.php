<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enhanced tenant management with business admin capabilities
     */
    public function up(): void
    {
        // Enhanced tenants table
        Schema::table('tenants', function (Blueprint $table) {
            // Business information
            $table->string('contact_email')->nullable()->after('email');
            $table->string('contact_phone')->nullable()->after('phone');
            $table->string('registration_number')->nullable()->after('address');
            $table->text('description')->nullable()->after('registration_number');
            $table->string('logo_path')->nullable()->after('description');
            
            // Subscription management
            $table->integer('max_users')->default(5)->after('subscription_plan');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_expires_at');
            $table->json('features')->nullable()->after('trial_ends_at'); // enabled features
            
            // Tenant configuration
            $table->string('timezone')->default('UTC')->after('features');
            $table->string('currency', 3)->default('RWF')->after('timezone');
            $table->string('locale')->default('en')->after('currency');
            
            // Security & compliance
            $table->boolean('enforce_2fa')->default(false)->after('locale');
            $table->integer('session_timeout')->default(120)->after('enforce_2fa'); // minutes
            $table->timestamp('last_backup_at')->nullable()->after('session_timeout');
            
            // Indexes
            $table->index(['status', 'subscription_plan']);
            $table->index(['created_at', 'status']);
        });

        // Enhanced user invitations system
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->string('email');
            $table->string('role')->default('user'); // admin, manager, accountant, user
            $table->boolean('is_admin')->default(false); // business admin flag
            $table->string('token', 64)->unique();
            $table->json('permissions')->nullable(); // specific permissions
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, expired, cancelled
            $table->json('metadata')->nullable(); // additional invitation data
            $table->timestamps();

            $table->unique(['tenant_id', 'email', 'status']); // prevent duplicate pending invites
            $table->index(['token', 'expires_at']);
            $table->index(['tenant_id', 'status']);
        });

        // Enhanced user sessions tracking
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->ipAddress('ip_address');
            $table->text('user_agent');
            $table->timestamp('last_activity');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['user_id', 'tenant_id', 'is_active']);
            $table->index(['last_activity', 'is_active']);
        });

        // Business admin permissions
        Schema::create('business_admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('permission'); // invite_users, manage_roles, view_audit_logs, etc.
            $table->json('constraints')->nullable(); // permission constraints
            $table->timestamp('granted_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tenant_id', 'permission']);
            $table->index(['tenant_id', 'permission']);
        });

        // Audit logs for compliance
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // login, logout, create_user, delete_project, etc.
            $table->string('resource_type')->nullable(); // User, Project, Client, etc.
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->text('description');
            $table->json('metadata')->nullable(); // old_values, new_values, etc.
            $table->string('severity')->default('low'); // low, medium, high, critical
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
            $table->index(['user_id', 'action']);
            $table->index(['resource_type', 'resource_id']);
            $table->index(['severity', 'created_at']);
        });

        // Rate limiting for security
        Schema::create('rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('key'); // user_id:action or ip:action
            $table->string('action'); // login, api_call, invite_user, etc.
            $table->integer('attempts')->default(1);
            $table->timestamp('window_start');
            $table->timestamp('blocked_until')->nullable();
            $table->timestamps();

            $table->unique(['key', 'action', 'window_start']);
            $table->index(['blocked_until']);
        });

        // Two-factor authentication
        Schema::create('two_factor_auth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('secret');
            $table->json('recovery_codes');
            $table->timestamp('enabled_at');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_auth');
        Schema::dropIfExists('rate_limits');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('business_admin_permissions');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('user_invitations');
        
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'contact_email', 'contact_phone', 'registration_number', 'description', 'logo_path',
                'max_users', 'trial_ends_at', 'features', 'timezone', 'currency', 'locale',
                'enforce_2fa', 'session_timeout', 'last_backup_at'
            ]);
        });
    }
};