<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use App\Models\Client;
use App\Traits\Downloadable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    use Downloadable;
    /**
     * Items per page for pagination.
     */
    protected int $perPage = 15;

    /**
     * Display a listing of the expenses.
     *
     * Also prepares a minimal daily-by-category stats structure:
     *  - $categories: array of distinct categories
     *  - $dailyTotals: [ 'YYYY-MM-DD' => [ 'Category' => total, ... ], ... ]
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Paginated list for the table (eager load relationships)
        $expenses = Expense::with(['project', 'client', 'user'])
                    ->latest()
                    ->paginate($this->perPage);

        // Get distinct category list (so we can build columns in the stats table)
        $categories = Expense::select('category')
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category')
                        ->toArray();

        // Group by date (day only) and category, summing amounts.
        // Use DATE(`date`) to ensure grouping by date only if date is a datetime column.
        $rows = Expense::selectRaw('DATE(`date`) as day, category, SUM(amount) as total')
                ->groupBy('day', 'category')
                ->orderBy('day', 'desc')
                ->get();

        // Transform into [ 'YYYY-MM-DD' => [ 'Category' => total, ... ], ... ]
        $dailyTotals = [];
        foreach ($rows as $r) {
            $day = (string) $r->day; // YYYY-MM-DD
            // ensure an array exists for the day
            if (! isset($dailyTotals[$day])) {
                $dailyTotals[$day] = [];
            }
            $dailyTotals[$day][ $r->category ] = (float) $r->total;
        }

        return view('expenses.index', compact('expenses', 'categories', 'dailyTotals'));
    }

    /**
     * Show the form for creating a new expense.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $projects = Project::orderBy('name')->pluck('name', 'id');
        $clients  = Client::orderBy('name')->pluck('name', 'id');

        return view('expenses.create', compact('projects', 'clients'));
    }

    /**
     * Store a newly created expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $this->validateExpense($request);

        $data = $this->ensureTenantId($data);
        Expense::create($data);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    /**
     * Display the specified expense.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\View\View
     */
    public function show(Expense $expense)
    {
        // If you want to ensure project/client/user are loaded:
        $expense->load(['project', 'client', 'user']);

        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\View\View
     */
    public function edit(Expense $expense)
    {
        $projects = Project::orderBy('name')->pluck('name', 'id');
        $clients  = Client::orderBy('name')->pluck('name', 'id');

        return view('expenses.edit', compact('expense', 'projects', 'clients'));
    }

    /**
     * Update the specified expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Expense $expense)
    {
        $data = $this->validateExpense($request);

        $expense->update($data);

        return redirect()
            ->route('expenses.show', $expense)
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Validate expense request data (shared between store & update).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function validateExpense(Request $request): array
    {
        return $request->validate([
            'date'        => 'required|date',
            'category'    => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id'  => 'nullable|exists:projects,id',
            'client_id'   => 'nullable|exists:clients,id',
            'amount'      => 'required|numeric',
            'method'      => 'nullable|string|max:255',
            'status'      => 'nullable|string|max:255',
            'user_id'     => 'nullable|exists:users,id',
        ]);
    }
    
    /**
     * Export expenses as CSV
     */
    public function exportCsv(Request $request)
    {
        // Check permission for expense export
        if (!Auth::user()->can('expenses.export')) {
            abort(403, 'You do not have permission to export expenses.');
        }
        
        $filename = $request->get('filename', 'expenses');
        
        $expenses = Expense::with(['project', 'client'])->latest()->get();
        
        $headers = [
            'id' => 'ID',
            'category' => 'Category',
            'description' => 'Description',
            'amount' => 'Amount (RWF)',
            'project_name' => 'Project',
            'client_name' => 'Client',
            'method' => 'Payment Method',
            'status' => 'Status',
            'created_at' => 'Created Date'
        ];
        
        // Transform data for CSV
        $csvData = $expenses->map(function ($expense) {
            return [
                'id' => $expense->id,
                'category' => $expense->category ?? 'N/A',
                'description' => $expense->description ?? 'N/A',
                'amount' => $expense->amount ?? 0,
                'project_name' => $expense->project ? $expense->project->name : 'N/A',
                'client_name' => $expense->client ? $expense->client->name : 'N/A',
                'method' => $expense->method ?? 'N/A',
                'status' => ucfirst($expense->status ?? 'completed'),
                'created_at' => $expense->created_at->format('Y-m-d H:i:s')
            ];
        });
        
        return $this->downloadCsv($csvData, $filename, array_keys($headers));
    }
    
    /**
     * Export expenses as PDF
     */
    public function exportPdf(Request $request)
    {
        // Check permission for expense export
        if (!Auth::user()->can('expenses.export')) {
            abort(403, 'You do not have permission to export expenses.');
        }
        
        $filename = $request->get('filename', 'expenses');
        
        $expenses = Expense::with(['project', 'client'])->latest()->get();
        
        $html = $this->generatePdfHtml('exports.financial-pdf', [
            'data' => $expenses,
            'title' => 'Expenses Report',
            'subtitle' => 'Complete list of all expenses',
            'totalRecords' => $expenses->count(),
            'showProject' => true
        ]);
        
        return $this->downloadPdf($html, $filename);
    }
}