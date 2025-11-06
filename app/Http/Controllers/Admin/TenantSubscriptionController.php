<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantSubscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Display a listing of tenant subscriptions.
     */
    public function index()
    {
        $subscriptions = TenantSubscription::with('tenant')
            ->orderBy('current_period_end', 'asc')
            ->paginate(20);

        $stats = [
            'total' => TenantSubscription::count(),
            'active' => TenantSubscription::where('status', TenantSubscription::STATUS_ACTIVE)->count(),
            'expiring_soon' => TenantSubscription::expiringSoon()->count(),
            'past_due' => TenantSubscription::where('status', TenantSubscription::STATUS_PAST_DUE)->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    /**
     * Show the form for creating a new subscription.
     */
    public function create()
    {
        $tenants = Tenant::whereDoesntHave('subscription')->get();
        $plans = TenantSubscription::PLANS_CONFIG;

        return view('admin.subscriptions.create', compact('tenants', 'plans'));
    }

    /**
     * Store a newly created subscription.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'plan' => 'required|in:' . implode(',', array_keys(TenantSubscription::PLANS_CONFIG)),
            'billing_cycle' => 'required|in:monthly,yearly',
            'start_date' => 'required|date',
        ]);

        $planConfig = TenantSubscription::PLANS_CONFIG[$request->plan];
        $priceKey = $request->billing_cycle === 'yearly' ? 'yearly_price' : 'monthly_price';
        $amount = $planConfig[$priceKey];

        $startDate = now()->parse($request->start_date);
        $endDate = $request->billing_cycle === 'yearly' 
            ? $startDate->copy()->addYear()
            : $startDate->copy()->addMonth();

        $subscription = TenantSubscription::create([
            'tenant_id' => $request->tenant_id,
            'plan' => $request->plan,
            'billing_cycle' => $request->billing_cycle,
            'status' => TenantSubscription::STATUS_ACTIVE,
            'amount' => $amount,
            'currency' => 'USD',
            'current_period_start' => $startDate,
            'current_period_end' => $endDate,
            'features' => $planConfig['features'],
            'usage_limits' => $planConfig['features'],
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription created successfully.');
    }

    /**
     * Display the specified subscription.
     */
    public function show(TenantSubscription $subscription)
    {
        $subscription->load('tenant');
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified subscription.
     */
    public function edit(TenantSubscription $subscription)
    {
        $plans = TenantSubscription::PLANS_CONFIG;
        return view('admin.subscriptions.edit', compact('subscription', 'plans'));
    }

    /**
     * Update the specified subscription.
     */
    public function update(Request $request, TenantSubscription $subscription)
    {
        $request->validate([
            'plan' => 'required|in:' . implode(',', array_keys(TenantSubscription::PLANS_CONFIG)),
            'billing_cycle' => 'required|in:monthly,yearly',
            'status' => 'required|in:active,suspended,cancelled,past_due,paused',
        ]);

        $planConfig = TenantSubscription::PLANS_CONFIG[$request->plan];
        $priceKey = $request->billing_cycle === 'yearly' ? 'yearly_price' : 'monthly_price';

        $subscription->update([
            'plan' => $request->plan,
            'billing_cycle' => $request->billing_cycle,
            'status' => $request->status,
            'amount' => $planConfig[$priceKey],
            'features' => $planConfig['features'],
            'usage_limits' => $planConfig['features'],
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription updated successfully.');
    }

    /**
     * Remove the specified subscription.
     */
    public function destroy(TenantSubscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription deleted successfully.');
    }

    /**
     * Upgrade subscription plan
     */
    public function upgrade(Request $request, TenantSubscription $subscription)
    {
        $request->validate([
            'plan' => 'required|in:' . implode(',', array_keys(TenantSubscription::PLANS_CONFIG)),
        ]);

        $planConfig = TenantSubscription::PLANS_CONFIG[$request->plan];
        $priceKey = $subscription->billing_cycle === 'yearly' ? 'yearly_price' : 'monthly_price';

        $subscription->update([
            'plan' => $request->plan,
            'amount' => $planConfig[$priceKey],
            'features' => $planConfig['features'],
            'usage_limits' => $planConfig['features'],
        ]);

        return back()->with('success', 'Subscription upgraded successfully.');
    }

    /**
     * Suspend subscription
     */
    public function suspend(TenantSubscription $subscription)
    {
        $subscription->pause();
        return back()->with('success', 'Subscription suspended successfully.');
    }

    /**
     * Resume subscription
     */
    public function resume(TenantSubscription $subscription)
    {
        $subscription->resume();
        return back()->with('success', 'Subscription resumed successfully.');
    }

    /**
     * Cancel subscription
     */
    public function cancel(TenantSubscription $subscription)
    {
        $subscription->cancel();
        return back()->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Renew subscription
     */
    public function renew(TenantSubscription $subscription)
    {
        $subscription->renew();
        return back()->with('success', 'Subscription renewed successfully.');
    }
}
