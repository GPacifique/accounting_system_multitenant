<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    // Uncomment to require authentication:
    // public function __construct() { $this->middleware('auth'); }

    /**
     * Display a listing of the resource with simple filters.
     */
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($b) use ($q) {
                $b->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%")
                  ->orWhere('category', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'nullable|date',
            'category' => 'nullable|string|max:100',
        ]);

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Expense added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'nullable|date',
            'category' => 'nullable|string|max:100',
        ]);

        $expense->update($data);

        return redirect()->route('expenses.show', $expense)->with('success', 'Expense updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }

    /**
     * Export filtered expenses as CSV.
     * Example route: GET /expenses/export
     */
    public function export(Request $request): StreamedResponse
    {
        $fileName = 'expenses_' . now()->format('Ymd_His') . '.csv';

        $query = Expense::query();

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->to);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get([
            'expense_date',
            'title',
            'category',
            'amount',
            'description',
            'created_at',
        ]);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($expenses) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel to detect UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, ['Date', 'Title', 'Category', 'Amount', 'Description', 'Created At']);

            foreach ($expenses as $e) {
                fputcsv($handle, [
                    $e->expense_date?->format('Y-m-d') ?? '',
                    $e->title,
                    $e->category ?? '',
                    $e->amount,
                    $e->description ?? '',
                    $e->created_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
