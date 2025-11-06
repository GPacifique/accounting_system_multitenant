<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Project;
use App\Traits\Downloadable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    use Downloadable;
    /**
     * Helper function to check if table exists
     */
    private function has(string $table, ?string $column = null): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }
        return $column ? Schema::hasColumn($table, $column) : true;
    }

    /**
     * Display a listing of reports/financial summary
     */
    public function index(Request $request)
    {
        // Date filter
        $date = $request->input('date', now()->toDateString());
        $reportDate = Carbon::createFromFormat('Y-m-d', $date);
        $dateStart = $reportDate->startOfDay();
        $dateEnd = $reportDate->endOfDay();

        // Total workers
        $totalWorkers = $this->has('workers') ? Worker::count() : 0;

        // Income for the day
        $incomeToday = $this->has('incomes', 'amount_received') && $this->has('incomes', 'received_at')
            ? Income::whereBetween('received_at', [$dateStart, $dateEnd])->sum('amount_received')
            : 0;

        // Expenses for the day
        $expensesToday = $this->has('expenses', 'amount') && $this->has('expenses', 'created_at')
            ? Expense::whereBetween('created_at', [$dateStart, $dateEnd])->sum('amount')
            : 0;

        // Payments for the day
        $paymentsToday = $this->has('payments', 'amount') && $this->has('payments', 'created_at')
            ? Payment::whereBetween('created_at', [$dateStart, $dateEnd])->sum('amount')
            : 0;

        // Worker Payments (daily wages) for the day and month
        $workerPaymentsToday = $this->has('worker_payments', 'amount') && $this->has('worker_payments', 'paid_on')
            ? DB::table('worker_payments')->whereDate('paid_on', $reportDate->toDateString())->sum('amount')
            : 0;

        // Recent transactions for the day
        $recentIncomes = $this->has('incomes')
            ? Income::whereBetween('received_at', [$dateStart, $dateEnd])->latest()->limit(10)->get()
            : collect();

        $recentExpenses = $this->has('expenses')
            ? Expense::whereBetween('created_at', [$dateStart, $dateEnd])->latest()->limit(10)->get()
            : collect();

        $recentPayments = $this->has('payments')
            ? Payment::whereBetween('created_at', [$dateStart, $dateEnd])->latest()->limit(10)->get()
            : collect();

        // Recent Worker Payments (with worker name)
        $recentWorkerPayments = $this->has('worker_payments')
            ? DB::table('worker_payments as wp')
                ->join('workers as w', 'w.id', '=', 'wp.worker_id')
                ->whereDate('wp.paid_on', $reportDate->toDateString())
                ->orderByDesc('wp.paid_on')
                ->orderByDesc('wp.id')
                ->limit(10)
                ->get(['wp.paid_on', 'wp.amount', 'w.first_name', 'w.last_name', 'w.id as worker_id'])
            : collect();

        // Month summary
        $monthStart = $reportDate->copy()->startOfMonth();
        $monthEnd = $reportDate->copy()->endOfMonth();

        $incomeThisMonth = $this->has('incomes', 'amount_received')
            ? Income::whereBetween('received_at', [$monthStart, $monthEnd])->sum('amount_received')
            : 0;

        $expensesThisMonth = $this->has('expenses', 'amount')
            ? Expense::whereBetween('created_at', [$monthStart, $monthEnd])->sum('amount')
            : 0;

        $paymentsThisMonth = $this->has('payments', 'amount')
            ? Payment::whereBetween('created_at', [$monthStart, $monthEnd])->sum('amount')
            : 0;

        $workerPaymentsThisMonth = $this->has('worker_payments', 'amount') && $this->has('worker_payments', 'paid_on')
            ? DB::table('worker_payments')->whereBetween('paid_on', [$monthStart->toDateString(), $monthEnd->toDateString()])->sum('amount')
            : 0;

        // Worker pay per position (today and this month)
        $workerPayByPositionToday = $this->has('worker_payments') && $this->has('workers')
            ? DB::table('worker_payments as wp')
                ->join('workers as w', 'w.id', '=', 'wp.worker_id')
                ->whereDate('wp.paid_on', $reportDate->toDateString())
                ->groupBy('w.position')
                ->select('w.position', DB::raw('SUM(wp.amount) as total'))
                ->orderByDesc('total')
                ->get()
            : collect();

        $workerPayByPositionMonth = $this->has('worker_payments') && $this->has('workers')
            ? DB::table('worker_payments as wp')
                ->join('workers as w', 'w.id', '=', 'wp.worker_id')
                ->whereBetween('wp.paid_on', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->groupBy('w.position')
                ->select('w.position', DB::raw('SUM(wp.amount) as total'))
                ->orderByDesc('total')
                ->get()
            : collect();

        // Projects for the period
        $projectsCount = $this->has('projects')
            ? Project::count()
            : 0;

        $projectsThisMonth = $this->has('projects')
            ? Project::whereBetween('created_at', [$monthStart, $monthEnd])->count()
            : 0;

        // Income by category
        $incomeByCategory = $this->has('incomes') && Schema::hasColumn('incomes', 'category')
            ? DB::table('incomes')
                ->select('category', DB::raw('SUM(amount_received) as total'))
                ->groupBy('category')
                ->get()
            : collect();

        // Expense by category
        $expenseByCategory = $this->has('expenses') && Schema::hasColumn('expenses', 'category')
            ? DB::table('expenses')
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->get()
            : collect();

        return view('reports.index', compact(
            'totalWorkers',
            'incomeToday', 'expensesToday', 'paymentsToday',
            'incomeThisMonth', 'expensesThisMonth', 'paymentsThisMonth',
            'recentIncomes', 'recentExpenses', 'recentPayments',
            'projectsCount', 'projectsThisMonth',
            'incomeByCategory', 'expenseByCategory',
            'workerPaymentsToday', 'workerPaymentsThisMonth', 'recentWorkerPayments',
            'workerPayByPositionToday', 'workerPayByPositionMonth',
            'date'
        ));
    }

    /**
     * Show the form for creating a new report
     */
    public function create()
    {
        return view('reports.create');
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'report_date' => 'nullable|date',
        ]);

        if (!$this->has('reports')) {
            return back()->withError('Reports table not found');
        }

    $report = new \App\Models\Report($validated);
    $report->user_id = Auth::id();
        $report->save();

        return redirect()->route('reports.show', $report)->withSuccess('Report created successfully');
    }

    /**
     * Display the specified report
     */
    public function show($id)
    {
        if (!$this->has('reports')) {
            return back()->withError('Reports table not found');
        }

        $report = \App\Models\Report::findOrFail($id);
        return view('reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified report
     */
    public function edit($id)
    {
        if (!$this->has('reports')) {
            return back()->withError('Reports table not found');
        }

        $report = \App\Models\Report::findOrFail($id);
        $projects = $this->has('projects') ? Project::all() : collect();
        return view('reports.edit', compact('report', 'projects'));
    }

    /**
     * Update the specified report
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'report_date' => 'nullable|date',
        ]);

        if (!$this->has('reports')) {
            return back()->withError('Reports table not found');
        }

        $report = \App\Models\Report::findOrFail($id);
        $report->update($validated);

        return redirect()->route('reports.show', $report)->withSuccess('Report updated successfully');
    }

    /**
     * Remove the specified report
     */
    public function destroy($id)
    {
        if (!$this->has('reports')) {
            return back()->withError('Reports table not found');
        }

        $report = \App\Models\Report::findOrFail($id);
        $report->delete();

        return redirect()->route('reports.index')->withSuccess('Report deleted successfully');
    }
    
    /**
     * Export financial reports as CSV
     */
    public function exportCsv(Request $request)
    {
        // Check permission for report export
        if (!Auth::user()->can('reports.export')) {
            abort(403, 'You do not have permission to export reports.');
        }
        
        $filename = $request->get('filename', 'financial_reports');
        $date = $request->get('date', Carbon::today()->toDateString());
        
        // Get comprehensive financial data for the report
        $reportData = $this->getFinancialReportData($date);
        
        $headers = [
            'type' => 'Type',
            'description' => 'Description',
            'amount' => 'Amount (RWF)',
            'date' => 'Date',
            'project' => 'Project',
            'category' => 'Category',
            'status' => 'Status'
        ];
        
        // Combine all financial data into a single export
        $csvData = collect();
        
        // Add income records
        if ($this->has('incomes')) {
            $incomes = Income::with('project')
                ->whereDate('received_at', $date)
                ->get();
            
            foreach ($incomes as $income) {
                $csvData->push([
                    'type' => 'Income',
                    'description' => $income->description ?? 'Income received',
                    'amount' => $income->amount_received ?? 0,
                    'date' => $income->received_at ? Carbon::parse($income->received_at)->format('Y-m-d') : $date,
                    'project' => $income->project ? $income->project->name : 'N/A',
                    'category' => 'Revenue',
                    'status' => ucfirst($income->status ?? 'completed')
                ]);
            }
        }
        
        // Add expense records
        if ($this->has('expenses')) {
            $expenses = Expense::with('project')
                ->whereDate('created_at', $date)
                ->get();
            
            foreach ($expenses as $expense) {
                $csvData->push([
                    'type' => 'Expense',
                    'description' => $expense->description ?? 'Expense recorded',
                    'amount' => '-' . ($expense->amount ?? 0), // Negative for expenses
                    'date' => $expense->created_at->format('Y-m-d'),
                    'project' => $expense->project ? $expense->project->name : 'N/A',
                    'category' => $expense->category ?? 'General',
                    'status' => ucfirst($expense->status ?? 'completed')
                ]);
            }
        }
        
        // Add payment records
        if ($this->has('payments')) {
            $payments = Payment::with('employee')
                ->whereDate('created_at', $date)
                ->get();
            
            foreach ($payments as $payment) {
                $csvData->push([
                    'type' => 'Payment',
                    'description' => 'Payment to ' . ($payment->employee ? $payment->employee->name : 'Employee'),
                    'amount' => '-' . ($payment->amount ?? 0), // Negative for payments
                    'date' => $payment->created_at->format('Y-m-d'),
                    'project' => 'N/A',
                    'category' => 'Payroll',
                    'status' => ucfirst($payment->status ?? 'completed')
                ]);
            }
        }
        
        return $this->downloadCsv($csvData, $filename, array_keys($headers));
    }
    
    /**
     * Export financial reports as PDF
     */
    public function exportPdf(Request $request)
    {
        // Check permission for report export
        if (!Auth::user()->can('reports.export')) {
            abort(403, 'You do not have permission to export reports.');
        }
        
        $filename = $request->get('filename', 'financial_reports');
        $date = $request->get('date', Carbon::today()->toDateString());
        
        // Get comprehensive financial data for the report
        $reportData = $this->getFinancialReportData($date);
        
        $html = $this->generatePdfHtml('exports.reports-pdf', [
            'data' => $reportData,
            'title' => 'Financial Reports',
            'subtitle' => 'Daily financial summary for ' . Carbon::parse($date)->format('F j, Y'),
            'date' => $date,
            'totalRecords' => $reportData['totalTransactions'] ?? 0
        ]);
        
        return $this->downloadPdf($html, $filename);
    }
    
    /**
     * Get comprehensive financial report data
     */
    private function getFinancialReportData($date)
    {
        $data = [
            'incomes' => collect(),
            'expenses' => collect(),
            'payments' => collect(),
            'totalIncome' => 0,
            'totalExpenses' => 0,
            'totalPayments' => 0,
            'netAmount' => 0,
            'totalTransactions' => 0
        ];
        
        // Get income data
        if ($this->has('incomes')) {
            $data['incomes'] = Income::with('project')
                ->whereDate('received_at', $date)
                ->get();
            $data['totalIncome'] = $data['incomes']->sum('amount_received');
        }
        
        // Get expense data
        if ($this->has('expenses')) {
            $data['expenses'] = Expense::with('project')
                ->whereDate('created_at', $date)
                ->get();
            $data['totalExpenses'] = $data['expenses']->sum('amount');
        }
        
        // Get payment data
        if ($this->has('payments')) {
            $data['payments'] = Payment::with('employee')
                ->whereDate('created_at', $date)
                ->get();
            $data['totalPayments'] = $data['payments']->sum('amount');
        }
        
        $data['netAmount'] = $data['totalIncome'] - $data['totalExpenses'] - $data['totalPayments'];
        $data['totalTransactions'] = $data['incomes']->count() + $data['expenses']->count() + $data['payments']->count();
        
        return $data;
    }
}
