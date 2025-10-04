<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Income;
use App\Models\Project;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        // Fetch project stats
        $projectStats = Income::select(
            'project_id',
            DB::raw('SUM(amount_received) as total_paid'),
            DB::raw("SUM(CASE WHEN payment_status!='paid' THEN amount_remaining ELSE 0 END) as total_remaining"),
            DB::raw('SUM(amount_received) as total_amount') // You can adjust this to include expenses if needed
        )
        ->groupBy('project_id')
        ->with('project')
        ->get();

        // Fetch incomes (paginated)
        $incomes = Income::with('project')->latest()->paginate(10);

        return view('incomes.index', compact('projectStats', 'incomes'));
    }

    public function create()
    {
        $projects = Project::all(); // For project dropdown
        return view('incomes.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'invoice_number' => 'required|string|max:255|unique:incomes,invoice_number',
            'amount_received' => 'required|numeric|min:0',
            'payment_status' => 'required|in:Paid,Pending,partially paid,Overdue',
            'amount_remaining' => 'required|numeric|min:0',
            'received_at' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        Income::create($validated);

        return redirect()->route('incomes.index')
                         ->with('success', 'Income record created successfully.');
    }

    public function show(Income $income)
    {
        $income->load('project');
        return view('incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        $projects = Project::all();
        return view('incomes.edit', compact('income', 'projects'));
    }

    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'invoice_number' => 'required|string|max:255|unique:incomes,invoice_number,' . $income->id,
            'amount_received' => 'required|numeric|min:0',
            'payment_status' => 'required|in:Paid,Pending,partially paid,Overdue',
            'amount_remaining' => 'required|numeric|min:0',
            'received_at' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $income->update($validated);

        return redirect()->route('incomes.index')
                         ->with('success', 'Income record updated successfully.');
    }

    public function destroy(Income $income)
    {
        $income->delete();

        return redirect()->route('incomes.index')
                         ->with('success', 'Income record deleted successfully.');
    }
}
