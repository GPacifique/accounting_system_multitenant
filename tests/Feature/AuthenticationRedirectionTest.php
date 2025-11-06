<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AuthenticationRedirectionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $manager; 
    protected $accountant;
    protected $user;
    protected $tenant;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'manager', 'guard_name' => 'web']);
        Role::create(['name' => 'accountant', 'guard_name' => 'web']);
        Role::create(['name' => 'user', 'guard_name' => 'web']);

        // Create users with different roles
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'is_super_admin' => true
        ]);
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create([
            'email' => 'manager@test.com'
        ]);
        $this->manager->assignRole('manager');

        $this->accountant = User::factory()->create([
            'email' => 'accountant@test.com'
        ]);
        $this->accountant->assignRole('accountant');

        $this->user = User::factory()->create([
            'email' => 'user@test.com'
        ]);
        $this->user->assignRole('user');

        // Create a tenant
        $this->tenant = Tenant::factory()->create([
            'domain' => 'test-tenant.example.com'
        ]);

        // Add users to tenant
        $this->admin->addToTenant($this->tenant->id, 'admin', true);
        $this->manager->addToTenant($this->tenant->id, 'manager', false);
        $this->accountant->addToTenant($this->tenant->id, 'accountant', false);
        $this->user->addToTenant($this->tenant->id, 'user', false);
    }

    /** @test */
    public function admin_redirected_to_admin_dashboard()
    {
        $this->actingAs($this->admin);
        
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/admin/dashboard');
    }

    /** @test */
    public function manager_redirected_to_manager_dashboard()
    {
        $this->actingAs($this->manager);
        
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/manager/dashboard');
    }

    /** @test */
    public function accountant_redirected_to_accountant_dashboard()
    {
        $this->actingAs($this->accountant);
        
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/accountant/dashboard');
    }

    /** @test */
    public function user_redirected_to_user_dashboard()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/user/dashboard');
    }

    /** @test */
    public function super_admin_can_access_any_tenant()
    {
        $this->actingAs($this->admin);
        
        // Super admin should be able to access tenant routes
        $response = $this->get("/tenant/{$this->tenant->id}/dashboard");
        
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_access_own_tenant()
    {
        $this->actingAs($this->manager);
        
        // User should be able to access tenant they belong to
        $response = $this->get("/tenant/{$this->tenant->id}/dashboard");
        
        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_access_other_tenant()
    {
        $otherTenant = Tenant::factory()->create([
            'domain' => 'other-tenant.example.com'
        ]);
        
        $this->actingAs($this->user);
        
        // User should not be able to access tenant they don't belong to
        $response = $this->get("/tenant/{$otherTenant->id}/dashboard");
        
        $response->assertRedirect('/dashboard');
        $response->assertSessionHasErrors(['access']);
    }

    /** @test */
    public function api_returns_json_for_unauthorized_tenant_access()
    {
        $otherTenant = Tenant::factory()->create([
            'domain' => 'other-tenant.example.com'
        ]);
        
        $this->actingAs($this->user);
        
        // API request should return JSON response
        $response = $this->getJson("/api/tenant/{$otherTenant->id}/users");
        
        $response->assertStatus(403)
                ->assertJson([
                    'error' => 'Access denied',
                    'message' => 'You do not have access to this tenant'
                ]);
    }

    /** @test */
    public function unauthenticated_user_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function api_returns_json_for_unauthenticated()
    {
        $response = $this->getJson('/api/user');
        
        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Authentication required'
                ]);
    }

    /** @test */
    public function user_without_permissions_redirected_to_welcome()
    {
        $noPermUser = User::factory()->create([
            'email' => 'noperm@test.com'
        ]);
        // Don't assign any roles or permissions
        
        $this->actingAs($noPermUser);
        
        $response = $this->get('/projects');
        
        $response->assertRedirect('/welcome')
                ->assertSessionHas('warning', 'You need additional permissions to access that section. Please contact your administrator.');
    }

    /** @test */
    public function user_can_access_allowed_routes_without_permissions()
    {
        $noPermUser = User::factory()->create([
            'email' => 'noperm@test.com'
        ]);
        
        $this->actingAs($noPermUser);
        
        // These routes should be accessible without special permissions
        $allowedRoutes = [
            '/welcome',
            '/profile',
        ];
        
        foreach ($allowedRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function tenant_role_is_set_in_request_attributes()
    {
        $this->actingAs($this->manager);
        
        $response = $this->get("/tenant/{$this->tenant->id}/dashboard");
        
        // Check that the middleware sets the tenant role
        $this->assertEquals('manager', request()->attributes->get('user_tenant_role'));
    }

    /** @test */
    public function business_permissions_work_correctly()
    {
        // Grant specific business permission to user
        $this->user->grantBusinessPermission('invite_users', $this->tenant->id);
        
        $this->assertTrue($this->user->canInviteUsers($this->tenant->id));
        $this->assertFalse($this->user->canManageUsers($this->tenant->id));
    }

    /** @test */
    public function user_can_invite_when_has_permission()
    {
        $this->actingAs($this->admin);
        
        // Admin should be able to invite users
        $this->assertTrue($this->admin->canInviteUsers($this->tenant->id));
        
        $invitation = $this->admin->inviteUserToTenant(
            $this->tenant->id,
            'newuser@test.com',
            'user'
        );
        
        $this->assertNotNull($invitation);
        $this->assertEquals('newuser@test.com', $invitation->email);
        $this->assertEquals('user', $invitation->role);
    }

    /** @test */
    public function user_cannot_invite_without_permission()
    {
        $this->actingAs($this->user);
        
        $this->assertFalse($this->user->canInviteUsers($this->tenant->id));
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User does not have permission to invite users');
        
        $this->user->inviteUserToTenant(
            $this->tenant->id,
            'newuser@test.com',
            'user'
        );
    }
}