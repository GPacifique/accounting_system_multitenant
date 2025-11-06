<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\Project;
use App\Traits\Downloadable;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    use Downloadable;
    public function index()
    {
        // Fetch project stats (aligned with dashboard/project calculations)
        // total_paid: sum of incomes.amount_received per project
        // total_remaining: project.contract_value - sum(incomes.amount_received)
        // total_amount: project.contract_value
        $projectStats = DB::table('projects')
            ->leftJoin('incomes', 'projects.id', '=', 'incomes.project_id')
            ->select(
                'projects.id as project_id',
                'projects.name as project_name',
                DB::raw('COALESCE(SUM(incomes.amount_received), 0) as total_paid'),
                DB::raw('COALESCE(projects.contract_value, 0) as total_amount'),
                DB::raw('(COALESCE(projects.contract_value, 0) - COALESCE(SUM(incomes.amount_received), 0)) as total_remaining')
            )
            ->groupBy('projects.id', 'projects.name', 'projects.contract_value')
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
    
    /**
     * Export incomes as CSV
     */
    public function exportCsv(Request $request)
    {
        // Check permission for income export
        if (!Auth::user()->can('incomes.export')) {
            abort(403, 'You do not have permission to export incomes.');
        }
        
        $filename = $request->get('filename', 'incomes');
        
        $incomes = Income::with('project')->latest()->get();
        
        $headers = [
            'id' => 'ID',
            'project_name' => 'Project',
            'amount_received' => 'Amount Received (RWF)',
            'received_at' => 'Received Date',
            'payment_method' => 'Payment Method',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created Date'
        ];
        
        // Transform data for CSV
        $csvData = $incomes->map(function ($income) {
            return [
                'id' => $income->id,
                'project_name' => $income->project ? $income->project->name : 'N/A',
                'amount_received' => $income->amount_received ?? 0,
                'received_at' => $income->received_at ? \Carbon\Carbon::parse($income->received_at)->format('Y-m-d') : 'N/A',
                'payment_method' => $income->payment_method ?? 'N/A',
                'description' => $income->description ?? 'N/A',
                'status' => ucfirst($income->status ?? 'completed'),
                'created_at' => $income->created_at->format('Y-m-d H:i:s')
            ];
        });
        
        return $this->downloadCsv($csvData, $filename, array_keys($headers));
    }
    
    /**
     * Export incomes as PDF
     */
    public function exportPdf(Request $request)
    {
        // Check permission for income export
        if (!Auth::user()->can('incomes.export')) {
            abort(403, 'You do not have permission to export incomes.');
        }
        
        $filename = $request->get('filename', 'incomes');
        
        $incomes = Income::with('project')->latest()->get();
        
        $html = $this->generatePdfHtml('exports.financial-pdf', [
            'data' => $incomes,
            'title' => 'Income Report',
            'subtitle' => 'Complete list of all income records',
            'totalRecords' => $incomes->count(),
            'showProject' => true
        ]);
        
        return $this->downloadPdf($html, $filename);
    }
}
