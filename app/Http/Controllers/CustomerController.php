<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\BusinessQueryService;
use App\Services\TenantDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    protected BusinessQueryService $queryService;
    protected TenantDataService $tenantDataService;

    public function __construct(BusinessQueryService $queryService, TenantDataService $tenantDataService)
    {
        $this->middleware('auth');
        $this->middleware('tenant.data');
        $this->queryService = $queryService;
        $this->tenantDataService = $tenantDataService;
    }

    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        // Get customers with role-based filtering
        $customersQuery = $this->queryService->buildRoleBasedQuery('customers');
        
        // Apply search filters
        if ($request->filled('search')) {
            $customersQuery->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $customersQuery->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $customersQuery->where('customer_type', $request->type);
        }

        $customers = $customersQuery->paginate(15);

        // Get statistics
        $stats = [
            'total' => $this->queryService->buildRoleBasedQuery('customers')->count(),
            'active' => $this->queryService->buildRoleBasedQuery('customers')->where('status', 'active')->count(),
            'by_type' => $this->queryService->buildRoleBasedQuery('customers')
                              ->selectRaw('customer_type, COUNT(*) as count')
                              ->groupBy('customer_type')
                              ->pluck('count', 'customer_type')
                              ->toArray(),
        ];

        return view('customers.index', compact('customers', 'stats'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,NULL,id,tenant_id,' . app('currentTenant')->id,
            'phone' => 'nullable|string|max:20',
            'customer_type' => 'required|in:individual,business,enterprise',
            'company' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'active';
        $validated = $this->ensureTenantId($validated);

        $customer = Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        // Ensure customer belongs to current tenant and user has access
        $customerData = $this->queryService->buildRoleBasedQuery('customers')
                             ->where('id', $customer->id)
                             ->first();

        if (!$customerData) {
            abort(404, 'Customer not found or access denied.');
        }

        // Get customer's recent activity
        $recentInvoices = $this->queryService->buildRoleBasedQuery('invoices')
                               ->where('customer_id', $customer->id)
                               ->orderBy('created_at', 'desc')
                               ->limit(5)
                               ->get();

        $recentPayments = $this->queryService->buildRoleBasedQuery('payments')
                               ->where('customer_id', $customer->id)
                               ->orderBy('created_at', 'desc')
                               ->limit(5)
                               ->get();

        // Get customer statistics
        $stats = [
            'total_invoices' => $this->queryService->buildRoleBasedQuery('invoices')
                                     ->where('customer_id', $customer->id)
                                     ->count(),
            'total_paid' => $this->queryService->buildRoleBasedQuery('payments')
                                 ->where('customer_id', $customer->id)
                                 ->sum('amount'),
            'outstanding_balance' => $this->queryService->buildRoleBasedQuery('invoices')
                                          ->where('customer_id', $customer->id)
                                          ->where('status', '!=', 'paid')
                                          ->sum('total_amount'),
        ];

        return view('customers.show', compact('customer', 'recentInvoices', 'recentPayments', 'stats'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        // Ensure customer belongs to current tenant and user has access
        $customerData = $this->queryService->buildRoleBasedQuery('customers')
                             ->where('id', $customer->id)
                             ->first();

        if (!$customerData) {
            abort(404, 'Customer not found or access denied.');
        }

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer)
    {
        // Ensure customer belongs to current tenant and user has access
        $customerData = $this->queryService->buildRoleBasedQuery('customers')
                             ->where('id', $customer->id)
                             ->first();

        if (!$customerData) {
            abort(404, 'Customer not found or access denied.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id . ',id,tenant_id,' . app('currentTenant')->id,
            'phone' => 'nullable|string|max:20',
            'customer_type' => 'required|in:individual,business,enterprise',
            'company' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $validated['updated_by'] = Auth::id();

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        // Ensure customer belongs to current tenant and user has access
        $customerData = $this->queryService->buildRoleBasedQuery('customers')
                             ->where('id', $customer->id)
                             ->first();

        if (!$customerData) {
            abort(404, 'Customer not found or access denied.');
        }

        // Check if customer has invoices or payments
        $hasInvoices = $this->queryService->buildRoleBasedQuery('invoices')
                            ->where('customer_id', $customer->id)
                            ->exists();

        if ($hasInvoices) {
            return back()->with('error', 'Cannot delete customer with existing invoices. Please archive instead.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Export customers data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $customers = $this->queryService->buildRoleBasedQuery('customers')->get();

        if ($format === 'json') {
            return response()->json($customers);
        }

        // CSV export
        $filename = 'customers_' . app('currentTenant')->domain . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Company', 'Type', 
                'Credit Limit', 'Status', 'Created At'
            ]);

            // Data rows
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    $customer->company,
                    $customer->customer_type,
                    $customer->credit_limit,
                    $customer->status,
                    $customer->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Search customers.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $results = $this->queryService->buildRoleBasedQuery('customers')
                        ->where(function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->q . '%')
                                  ->orWhere('email', 'like', '%' . $request->q . '%')
                                  ->orWhere('company', 'like', '%' . $request->q . '%');
                        })
                        ->limit(20)
                        ->get();

        if ($request->expectsJson()) {
            return response()->json($results);
        }

        return view('customers.search', compact('results'));
    }
}
