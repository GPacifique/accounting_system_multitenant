<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Traits\Downloadable;

class PaymentController extends Controller
{
    use Downloadable;
    public function __construct()
    {
        // Protect with authentication (adjust as needed)
        $this->middleware('auth');
    }

    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $payments = Payment::with('employee')->latest()->paginate(10);
        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        return view('payments.create');
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string|max:50',
            'reference' => 'nullable|string|max:100',
            'status' => 'nullable|in:pending,completed,failed',
        ]);

        Payment::create($request->only('employee_id', 'amount', 'method', 'reference', 'status'));

        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load('employee');
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string|max:50',
            'reference' => 'nullable|string|max:100',
            'status' => 'nullable|in:pending,completed,failed',
        ]);

        $payment->update($request->only('employee_id', 'amount', 'method', 'reference', 'status'));

        return redirect()->route('payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
    
    /**
     * Export payments as CSV
     */
    public function exportCsv(Request $request)
    {
        // Check permission for payment export
        if (!Auth::user()->can('payments.export')) {
            abort(403, 'You do not have permission to export payments.');
        }
        
        $filename = $request->get('filename', 'payments');
        
        $payments = Payment::with('employee')->latest()->get();
        
        $headers = [
            'id' => 'ID',
            'employee_name' => 'Employee',
            'amount' => 'Amount (RWF)',
            'created_at' => 'Payment Date',
            'description' => 'Description',
            'status' => 'Status',
            'method' => 'Method'
        ];
        
        // Transform data for CSV
        $csvData = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'employee_name' => $payment->employee ? $payment->employee->name : 'N/A',
                'amount' => $payment->amount ?? 0,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'description' => $payment->reference ?? 'Payment',
                'status' => ucfirst($payment->status ?? 'completed'),
                'method' => $payment->method ?? 'N/A'
            ];
        });
        
        return $this->downloadCsv($csvData, $filename, array_keys($headers));
    }
    
    /**
     * Export payments as PDF
     */
    public function exportPdf(Request $request)
    {
        // Check permission for payment export
        if (!Auth::user()->can('payments.export')) {
            abort(403, 'You do not have permission to export payments.');
        }
        
        $filename = $request->get('filename', 'payments');
        
        $payments = Payment::with('employee')->latest()->get();
        
        $html = $this->generatePdfHtml('exports.financial-pdf', [
            'data' => $payments,
            'title' => 'Payments Report',
            'subtitle' => 'Complete list of all payments',
            'totalRecords' => $payments->count()
        ]);
        
        return $this->downloadPdf($html, $filename);
    }
}
