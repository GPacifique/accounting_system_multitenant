{{-- resources/views/exports/reports-pdf.blade.php --}}
@extends('exports.pdf-template')

@section('content')
{{-- Summary Section --}}
<div style="margin-bottom: 30px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
    <h3 style="margin: 0 0 15px 0; color: #333;">Financial Summary</h3>
    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
        <div style="width: 48%;">
            <table style="width: 100%; margin: 0;">
                <tr>
                    <td style="border: none; padding: 5px 0; font-weight: bold;">Total Income:</td>
                    <td style="border: none; padding: 5px 0; text-align: right; color: #059669;">
                        RWF {{ number_format($data['totalIncome'] ?? 0, 0) }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px 0; font-weight: bold;">Total Expenses:</td>
                    <td style="border: none; padding: 5px 0; text-align: right; color: #dc2626;">
                        RWF {{ number_format($data['totalExpenses'] ?? 0, 0) }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px 0; font-weight: bold;">Total Payments:</td>
                    <td style="border: none; padding: 5px 0; text-align: right; color: #dc2626;">
                        RWF {{ number_format($data['totalPayments'] ?? 0, 0) }}
                    </td>
                </tr>
            </table>
        </div>
        <div style="width: 48%;">
            <table style="width: 100%; margin: 0;">
                <tr>
                    <td style="border: none; padding: 5px 0; font-weight: bold;">Net Amount:</td>
                    <td style="border: none; padding: 5px 0; text-align: right; font-weight: bold; color: {{ ($data['netAmount'] ?? 0) >= 0 ? '#059669' : '#dc2626' }};">
                        RWF {{ number_format($data['netAmount'] ?? 0, 0) }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px 0; font-weight: bold;">Total Transactions:</td>
                    <td style="border: none; padding: 5px 0; text-align: right;">
                        {{ $data['totalTransactions'] ?? 0 }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

{{-- Income Section --}}
@if($data['incomes']->count() > 0)
<h3 style="color: #059669; margin: 30px 0 15px 0;">Income Records</h3>
<table>
    <thead>
        <tr>
            <th>Project</th>
            <th>Amount (RWF)</th>
            <th>Date</th>
            <th>Payment Method</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['incomes'] as $income)
        <tr>
            <td>{{ $income->project ? $income->project->name : 'N/A' }}</td>
            <td class="text-right amount">RWF {{ number_format($income->amount_received ?? 0, 0) }}</td>
            <td class="text-center">{{ $income->received_at ? \Carbon\Carbon::parse($income->received_at)->format('Y-m-d') : 'N/A' }}</td>
            <td>{{ $income->payment_method ?? 'N/A' }}</td>
            <td>{{ $income->description ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td><strong>Total Income:</strong></td>
            <td class="text-right amount"><strong>RWF {{ number_format($data['totalIncome'], 0) }}</strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
@endif

{{-- Expenses Section --}}
@if($data['expenses']->count() > 0)
<h3 style="color: #dc2626; margin: 30px 0 15px 0;">Expense Records</h3>
<table>
    <thead>
        <tr>
            <th>Category</th>
            <th>Amount (RWF)</th>
            <th>Project</th>
            <th>Method</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['expenses'] as $expense)
        <tr>
            <td>{{ $expense->category ?? 'General' }}</td>
            <td class="text-right amount">RWF {{ number_format($expense->amount ?? 0, 0) }}</td>
            <td>{{ $expense->project ? $expense->project->name : 'N/A' }}</td>
            <td>{{ $expense->method ?? 'N/A' }}</td>
            <td>{{ $expense->description ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td><strong>Total Expenses:</strong></td>
            <td class="text-right amount"><strong>RWF {{ number_format($data['totalExpenses'], 0) }}</strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
@endif

{{-- Payments Section --}}
@if($data['payments']->count() > 0)
<h3 style="color: #7c3aed; margin: 30px 0 15px 0;">Payment Records</h3>
<table>
    <thead>
        <tr>
            <th>Employee</th>
            <th>Amount (RWF)</th>
            <th>Date</th>
            <th>Description</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['payments'] as $payment)
        <tr>
            <td>{{ $payment->employee ? $payment->employee->name : 'N/A' }}</td>
            <td class="text-right amount">RWF {{ number_format($payment->amount ?? 0, 0) }}</td>
            <td class="text-center">{{ $payment->created_at->format('Y-m-d') }}</td>
            <td>{{ $payment->description ?? 'Payment' }}</td>
            <td class="text-center">{{ ucfirst($payment->status ?? 'completed') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td><strong>Total Payments:</strong></td>
            <td class="text-right amount"><strong>RWF {{ number_format($data['totalPayments'], 0) }}</strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
@endif

@if($data['incomes']->count() == 0 && $data['expenses']->count() == 0 && $data['payments']->count() == 0)
<div style="text-align: center; padding: 40px; color: #666;">
    <h3>No financial transactions found for this date</h3>
    <p>{{ $date ? \Carbon\Carbon::parse($date)->format('F j, Y') : 'Selected date' }}</p>
</div>
@endif
@endsection