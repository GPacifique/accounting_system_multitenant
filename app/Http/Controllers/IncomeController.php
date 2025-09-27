<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Project;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Display a listing of income records.
     */
    public function index()
    {
        $incomes = Income::with('project')->latest()->paginate(10);
        return view('incomes.index', compact('incomes'));
    }

    /**
     * Show the form for creating a new income record.
     */
    public function create()
    {
        $projects = Project::all(); // For project dropdown
        return view('incomes.create', compact('projects'));
    }

    /**
     * Store a newly created income record in storage.
     */
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

    /**
     * Display the specified income record.
     */
    public function show(Income $income)
    {
        $income->load('project');
        return view('incomes.show', compact('income'));
    }

    /**
     * Show the form for editing the specified income record.
     */
    public function edit(Income $income)
    {
        $projects = Project::all();
        return view('incomes.edit', compact('income', 'projects'));
    }

    /**
     * Update the specified income record in storage.
     */
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

    /**
     * Remove the specified income record from storage.
     */
    public function destroy(Income $income)
    {
        $income->delete();

        return redirect()->route('incomes.index')
                         ->with('success', 'Income record deleted successfully.');
    }
}
