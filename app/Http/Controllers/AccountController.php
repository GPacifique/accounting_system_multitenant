<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\TenantDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    protected TenantDataService $tenantDataService;

    public function __construct(TenantDataService $tenantDataService)
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\TenantDataMiddleware::class);
        $this->tenantDataService = $tenantDataService;
    }

    /**
     * Display a listing of accounts for the current tenant.
     */
    public function index(Request $request)
    {
        // Get filters from request
        $filters = [];
        if ($request->filled('type')) {
            $filters['type'] = $request->type;
        }
        if ($request->filled('is_active')) {
            $filters['is_active'] = $request->boolean('is_active');
        }

        // Get paginated accounts with tenant awareness
        $accounts = $this->tenantDataService->getPaginatedForCurrentTenant(
            Account::class,
            $perPage = 15,
            $relations = ['parent', 'creator'],
            $filters
        );

        // Get account types for filter dropdown
        $accountTypes = Account::TYPES;

        // Get statistics for current tenant
        $stats = [
            'total_accounts' => $this->tenantDataService->getForCurrentTenant(Account::class)->count(),
            'active_accounts' => $this->tenantDataService->getForCurrentTenant(
                Account::class,
                [],
                0
            )->where('is_active', true)->count(),
            'by_type' => $this->getAccountsByType(),
        ];

        return view('accounts.index', compact('accounts', 'accountTypes', 'stats'));
    }

    /**
     * Show the form for creating a new account.
     */
    public function create()
    {
        // Get parent accounts (for hierarchical structure)
        $parentAccounts = $this->tenantDataService->getForCurrentTenant(
            Account::class,
            [],
            10 // Cache for 10 minutes
        )->where('is_active', true);

        $accountTypes = Account::TYPES;

        return view('accounts.create', compact('parentAccounts', 'accountTypes'));
    }

    /**
     * Store a newly created account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code,NULL,id,tenant_id,' . app('currentTenant')->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Account::TYPES)),
            'parent_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'currency' => 'nullable|string|size:3',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        // Add tenant context and user info
        $validated['tenant_id'] = app('currentTenant')->id;
        $validated['created_by'] = Auth::id();
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;

        // Validate parent account belongs to same tenant
        if ($validated['parent_id']) {
            $parentAccount = $this->tenantDataService->findForCurrentTenant(
                Account::class,
                $validated['parent_id']
            );
            
            if (!$parentAccount) {
                return back()->withErrors(['parent_id' => 'Invalid parent account selected.']);
            }
        }

        $validated = $this->ensureTenantId($validated);
        $account = Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    /**
     * Display the specified account.
     */
    public function show(Account $account)
    {
        // Ensure account belongs to current tenant
        $account = $this->tenantDataService->findForCurrentTenant(
            Account::class,
            $account->id,
            ['parent', 'children', 'creator', 'updater']
        );

        if (!$account) {
            abort(404, 'Account not found or access denied.');
        }

        // Get account statistics
        $stats = [
            'balance' => $account->current_balance,
            'opening_balance' => $account->opening_balance,
            'has_transactions' => $account->hasTransactions(),
            'children_count' => $account->children()->count(),
        ];

        return view('accounts.show', compact('account', 'stats'));
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(Account $account)
    {
        // Ensure account belongs to current tenant
        $account = $this->tenantDataService->findForCurrentTenant(
            Account::class,
            $account->id
        );

        if (!$account) {
            abort(404, 'Account not found or access denied.');
        }

        // Get parent accounts (excluding self and descendants)
        $parentAccounts = $this->tenantDataService->getForCurrentTenant(Account::class)
            ->where('id', '!=', $account->id)
            ->where('is_active', true);

        $accountTypes = Account::TYPES;

        return view('accounts.edit', compact('account', 'parentAccounts', 'accountTypes'));
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, Account $account)
    {
        // Ensure account belongs to current tenant
        $account = $this->tenantDataService->findForCurrentTenant(
            Account::class,
            $account->id
        );

        if (!$account) {
            abort(404, 'Account not found or access denied.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code,' . $account->id . ',id,tenant_id,' . app('currentTenant')->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Account::TYPES)),
            'parent_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'currency' => 'nullable|string|size:3',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        // Validate parent account belongs to same tenant
        if ($validated['parent_id']) {
            $parentAccount = $this->tenantDataService->findForCurrentTenant(
                Account::class,
                $validated['parent_id']
            );
            
            if (!$parentAccount || $parentAccount->id === $account->id) {
                return back()->withErrors(['parent_id' => 'Invalid parent account selected.']);
            }
        }

        $validated['updated_by'] = Auth::id();
        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified account.
     */
    public function destroy(Account $account)
    {
        // Ensure account belongs to current tenant
        $account = $this->tenantDataService->findForCurrentTenant(
            Account::class,
            $account->id
        );

        if (!$account) {
            abort(404, 'Account not found or access denied.');
        }

        // Check if account can be deleted
        if ($account->is_system) {
            return back()->with('error', 'System accounts cannot be deleted.');
        }

        if ($account->hasTransactions()) {
            return back()->with('error', 'Cannot delete account with existing transactions.');
        }

        if ($account->children()->count() > 0) {
            return back()->with('error', 'Cannot delete account with child accounts.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }

    /**
     * Search accounts within current tenant.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $results = $this->tenantDataService->searchInTenant(
            Account::class,
            $request->q,
            ['code', 'name', 'description'],
            ['parent'],
            20
        );

        if ($request->expectsJson()) {
            return response()->json($results);
        }

        return view('accounts.search', compact('results'));
    }

    /**
     * Export accounts for current tenant.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $accounts = $this->tenantDataService->getForCurrentTenant(
            Account::class,
            ['parent', 'creator']
        );

        if ($format === 'json') {
            return response()->json($accounts);
        }

        // CSV export
        $filename = 'accounts_' . app('currentTenant')->domain . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($accounts) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Code', 'Name', 'Type', 'Parent', 'Description', 
                'Opening Balance', 'Current Balance', 'Currency', 'Active', 'Created At'
            ]);

            // Data rows
            foreach ($accounts as $account) {
                fputcsv($file, [
                    $account->code,
                    $account->name,
                    $account->type,
                    $account->parent ? $account->parent->name : '',
                    $account->description,
                    $account->opening_balance,
                    $account->current_balance,
                    $account->currency,
                    $account->is_active ? 'Yes' : 'No',
                    $account->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get accounts grouped by type for statistics.
     */
    protected function getAccountsByType(): array
    {
        $accounts = $this->tenantDataService->getForCurrentTenant(Account::class);
        
        return $accounts->groupBy('type')
                      ->map(function ($group) {
                          return $group->count();
                      })
                      ->toArray();
    }
}
