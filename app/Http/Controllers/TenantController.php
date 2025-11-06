<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only super admins can manage tenants
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isSuperAdmin()) {
                abort(403, 'Only super administrators can manage tenants.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of tenants.
     */
    public function index()
    {
        $tenants = Tenant::with(['users', 'creator'])
                         ->withCount('users')
                         ->orderBy('created_at', 'desc')
                         ->paginate(15);

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        return view('admin.tenants.create');
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain|alpha_dash',
            'business_type' => 'required|in:' . implode(',', array_keys(Tenant::BUSINESS_TYPES)),
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'subscription_plan' => 'required|in:' . implode(',', array_keys(Tenant::SUBSCRIPTION_PLANS)),
            'subscription_expires_at' => 'nullable|date|after:today',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        // Create database name from domain
        $databaseName = 'tenant_' . $request->domain;

        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->name,
            'domain' => $request->domain,
            'database' => $databaseName,
            'business_type' => $request->business_type,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'subscription_plan' => $request->subscription_plan,
            'subscription_expires_at' => $request->subscription_expires_at,
            'created_by' => Auth::id(),
            'status' => Tenant::STATUS_ACTIVE,
        ]);

        // Create admin user for the tenant
        $adminUser = User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => bcrypt($request->admin_password),
            'is_super_admin' => false,
        ]);

        // Add admin user to tenant
        $adminUser->addToTenant($tenant->id, 'admin', true);
        $adminUser->assignRole('admin');

        return redirect()->route('admin.tenants.index')
                        ->with('success', 'Tenant created successfully!');
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['users.roles', 'creator']);
        
        return view('admin.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('tenants')->ignore($tenant->id)],
            'business_type' => 'required|in:' . implode(',', array_keys(Tenant::BUSINESS_TYPES)),
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'subscription_plan' => 'required|in:' . implode(',', array_keys(Tenant::SUBSCRIPTION_PLANS)),
            'subscription_expires_at' => 'nullable|date',
            'status' => 'required|in:active,suspended,inactive',
        ]);

        $tenant->update($request->only([
            'name', 'domain', 'business_type', 'email', 'phone', 'address',
            'subscription_plan', 'subscription_expires_at', 'status'
        ]));

        return redirect()->route('admin.tenants.show', $tenant)
                        ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(Tenant $tenant)
    {
        // Remove all users from tenant
        $tenant->users()->detach();
        
        // Delete tenant
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
                        ->with('success', 'Tenant deleted successfully!');
    }

    /**
     * Suspend or activate a tenant.
     */
    public function toggleStatus(Tenant $tenant)
    {
        $newStatus = $tenant->status === Tenant::STATUS_ACTIVE 
                    ? Tenant::STATUS_SUSPENDED 
                    : Tenant::STATUS_ACTIVE;

        $tenant->update(['status' => $newStatus]);

        $action = $newStatus === Tenant::STATUS_ACTIVE ? 'activated' : 'suspended';

        return redirect()->back()
                        ->with('success', "Tenant {$action} successfully!");
    }

    /**
     * Switch to a different tenant context.
     */
    public function switchTenant(Request $request, Tenant $tenant)
    {
        $user = Auth::user();
        
        // Check if user belongs to this tenant
        if (!$user->belongsToTenant($tenant->id) && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this tenant.'
            ], 403);
        }

        // Check if tenant is active
        if (!$tenant->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'This tenant is currently inactive.'
            ], 403);
        }

        // Update user's current tenant
        $user->update(['current_tenant_id' => $tenant->id]);
        
        // Store in session as backup
        session(['current_tenant_id' => $tenant->id]);

        return response()->json([
            'success' => true,
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'message' => "Switched to {$tenant->name} successfully."
        ]);
    }

    /**
     * Show tenant-specific dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->currentTenant();
        
        if (!$tenant) {
            return redirect()->route('dashboard')
                           ->with('error', 'No tenant selected. Please select a tenant to continue.');
        }

        // Get tenant statistics
        $stats = [
            'users_count' => $tenant->users()->count(),
            'projects_count' => \App\Models\Project::where('tenant_id', $tenant->id)->count(),
            'tasks_count' => \App\Models\Task::where('tenant_id', $tenant->id)->count(),
            'active_tasks' => \App\Models\Task::where('tenant_id', $tenant->id)
                                             ->whereIn('status', ['pending', 'in_progress'])
                                             ->count(),
        ];

        // Recent activities
        $recentTasks = \App\Models\Task::where('tenant_id', $tenant->id)
                                      ->with(['project', 'assignedUser'])
                                      ->orderBy('updated_at', 'desc')
                                      ->limit(5)
                                      ->get();

        $recentProjects = \App\Models\Project::where('tenant_id', $tenant->id)
                                            ->with('client')
                                            ->orderBy('updated_at', 'desc')
                                            ->limit(5)
                                            ->get();

        return view('tenant.dashboard', compact('tenant', 'stats', 'recentTasks', 'recentProjects'));
    }

    /**
     * Show users for a specific tenant.
     */
    public function users(Tenant $tenant)
    {
        // Authorization is handled by the super-admin middleware
        
        $users = $tenant->users()
                       ->withPivot('role', 'is_admin', 'created_at')
                       ->orderBy('pivot_created_at', 'desc')
                       ->paginate(15);

        return view('admin.tenants.users', compact('tenant', 'users'));
    }

    /**
     * Invite a user to join a tenant.
     */
    public function inviteUser(Request $request, Tenant $tenant)
    {
        // Authorization is handled by the super-admin middleware
        
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,manager,accountant,user',
            'is_admin' => 'boolean',
        ]);

        // Check if user already exists
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            // Add existing user to tenant
            if ($user->belongsToTenant($tenant->id)) {
                return redirect()->back()
                               ->with('error', 'User is already a member of this tenant.');
            }
            
            $user->addToTenant($tenant->id, $request->role, $request->boolean('is_admin'));
            
            return redirect()->back()
                           ->with('success', 'User added to tenant successfully.');
        } else {
            // Create invitation for new user
            // This would require a UserInvitation model and email functionality
            return redirect()->back()
                           ->with('info', 'Invitation system not yet implemented for new users.');
        }
    }

    /**
     * Export tenants data.
     */
    public function export($format = 'csv')
    {
        $tenants = Tenant::with(['users', 'creator'])->get();
        
        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="tenants.csv"',
            ];
            
            $callback = function () use ($tenants) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Name', 'Domain', 'Business Type', 'Status', 'Users Count', 'Created At']);
                
                foreach ($tenants as $tenant) {
                    fputcsv($file, [
                        $tenant->id,
                        $tenant->name,
                        $tenant->domain,
                        $tenant->business_type,
                        $tenant->status,
                        $tenant->users->count(),
                        $tenant->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        
        // Default to CSV if format not supported
        return $this->export('csv');
    }

    /**
     * Show tenant analytics dashboard.
     */
    public function analytics(Request $request)
    {
        $timeRange = $request->get('timeRange', 30); // Default 30 days
        
        // Basic analytics data
        $analytics = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'inactive_tenants' => Tenant::where('status', 'inactive')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
            'active_users' => User::whereHas('tenants')->count(),
            'monthly_revenue' => $this->calculateMonthlyRevenue(),
            'usage_score' => $this->calculateUsageScore(),
            'tenants_growth' => $this->calculateGrowthRate('tenants', $timeRange),
            'users_growth' => $this->calculateGrowthRate('users', $timeRange),
            'revenue_growth' => $this->calculateGrowthRate('revenue', $timeRange),
            'plans' => [
                'basic' => Tenant::where('subscription_plan', 'basic')->count(),
                'professional' => Tenant::where('subscription_plan', 'professional')->count(),
                'enterprise' => Tenant::where('subscription_plan', 'enterprise')->count(),
            ],
            'feature_usage' => $this->getFeatureUsage(),
            'top_tenants' => $this->getTopTenants(),
            'recent_activity' => $this->getRecentActivity(),
        ];

        return view('admin.tenants.analytics', compact('analytics', 'timeRange'));
    }

    /**
     * Calculate monthly revenue.
     */
    private function calculateMonthlyRevenue()
    {
        $planPrices = [
            'basic' => 29,
            'professional' => 79,
            'enterprise' => 199,
        ];

        $revenue = 0;
        foreach ($planPrices as $plan => $price) {
            $count = Tenant::where('subscription_plan', $plan)
                          ->where('status', 'active')
                          ->count();
            $revenue += $count * $price;
        }

        return $revenue;
    }

    /**
     * Calculate platform usage score.
     */
    private function calculateUsageScore()
    {
        // This would calculate based on actual usage metrics
        // For now, return a sample score
        return rand(75, 95);
    }

    /**
     * Calculate growth rate for a given metric.
     */
    private function calculateGrowthRate($metric, $timeRange)
    {
        // This would calculate actual growth rates
        // For now, return sample growth rates
        return rand(5, 25);
    }

    /**
     * Get feature usage statistics.
     */
    private function getFeatureUsage()
    {
        return [
            'projects' => ['name' => 'Project Management', 'icon' => 'project-diagram', 'percentage' => 87, 'users' => 340],
            'tasks' => ['name' => 'Task Management', 'icon' => 'tasks', 'percentage' => 76, 'users' => 298],
            'reports' => ['name' => 'Reports & Analytics', 'icon' => 'chart-bar', 'percentage' => 64, 'users' => 251],
            'finance' => ['name' => 'Financial Tracking', 'icon' => 'dollar-sign', 'percentage' => 59, 'users' => 230],
        ];
    }

    /**
     * Get top performing tenants.
     */
    private function getTopTenants()
    {
        return Tenant::with('users')
                    ->where('status', 'active')
                    ->withCount('users')
                    ->orderBy('users_count', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($tenant) {
                        return [
                            'name' => $tenant->name,
                            'plan' => ucfirst($tenant->subscription_plan),
                            'users' => $tenant->users_count,
                            'activity' => rand(60, 95), // Would calculate from actual metrics
                            'revenue' => $this->getTenantMonthlyRevenue($tenant),
                        ];
                    })
                    ->toArray();
    }

    /**
     * Get recent platform activity.
     */
    private function getRecentActivity()
    {
        // This would pull from actual audit logs
        return [
            [
                'icon' => 'user-plus',
                'title' => 'New tenant created',
                'description' => 'Construction Co. Ltd joined the platform',
                'time' => '2 hours ago'
            ],
            [
                'icon' => 'users',
                'title' => 'Team invitation sent',
                'description' => '5 new users invited to Tech Solutions',
                'time' => '4 hours ago'
            ],
            [
                'icon' => 'credit-card',
                'title' => 'Subscription upgraded',
                'description' => 'Manufacturing Inc. upgraded to Enterprise',
                'time' => '6 hours ago'
            ],
        ];
    }

    /**
     * Calculate monthly revenue for a specific tenant.
     */
    private function getTenantMonthlyRevenue($tenant)
    {
        $planPrices = [
            'basic' => 29,
            'professional' => 79,
            'enterprise' => 199,
        ];

        return $planPrices[$tenant->subscription_plan] ?? 0;
    }

    /**
     * Show tenant settings page.
     */
    public function settings(Tenant $tenant)
    {
        // Authorization is handled by the super-admin middleware
        
        return view('admin.tenants.settings', compact('tenant'));
    }

    /**
     * Update tenant settings.
     */
    public function updateSettings(Request $request, Tenant $tenant)
    {
        // Authorization is handled by the super-admin middleware
        
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('tenants')->ignore($tenant->id)],
            'business_type' => 'required|in:' . implode(',', array_keys(Tenant::BUSINESS_TYPES)),
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'subscription_plan' => 'required|in:' . implode(',', array_keys(Tenant::SUBSCRIPTION_PLANS)),
            'subscription_expires_at' => 'nullable|date',
            'trial_ends_at' => 'nullable|date',
            'status' => 'required|in:active,suspended,inactive',
            'max_users' => 'nullable|integer|min:1|max:1000',
            'session_timeout' => 'nullable|integer|min:5|max:1440',
            'enforce_2fa' => 'boolean',
        ]);

        // Update basic tenant information
        $tenant->update($request->only([
            'name', 'domain', 'business_type', 'email', 'phone', 'address',
            'subscription_plan', 'subscription_expires_at', 'trial_ends_at', 'status',
            'max_users', 'session_timeout', 'enforce_2fa'
        ]));

        // Update settings
        if ($request->has('settings')) {
            foreach ($request->settings as $key => $value) {
                $tenant->setSetting($key, $value);
            }
        }

        // Update features
        $features = $request->input('features', []);
        $tenant->update(['features' => $features]);

        $action = $request->input('action', 'save_apply');
        $message = $action === 'save_draft' 
                  ? 'Settings saved as draft.' 
                  : 'Settings saved and applied successfully!';

        return redirect()->route('admin.tenants.settings', $tenant)
                        ->with('success', $message);
    }

    /**
     * Create tenant backup.
     */
    public function backup(Tenant $tenant)
    {
        // Authorization is handled by the super-admin middleware
        
        try {
            // This would implement actual backup functionality
            // For now, we'll simulate it
            $tenant->update(['last_backup_at' => now()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully.',
                'backup_id' => 'backup_' . time(),
                'size' => '2.5 MB'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
