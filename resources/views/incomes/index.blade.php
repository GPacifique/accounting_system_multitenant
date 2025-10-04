
@extends('layouts.app')
@vite(['resources/css/app.css', 'resources/js/app.js'])
@vite(['resources/css/app.css', 'resources/js/app.js'])
@section('content')
<div class="mb-6 bg-white rounded-lg shadow p-4">
    <h2 class="text-lg font-semibold mb-3">Project Totals</h2>

    @if($projectStats->isEmpty())
        <p class="text-sm text-gray-500">No project stats available.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 border-r text-sm text-gray-600">Project</th>
                        <th class="py-2 px-3 border-r text-sm text-gray-600">Total Paid</th>
                        <th class="py-2 px-3 border-r text-sm text-gray-600">Total Remaining</th>
                        <th class="py-2 px-3 text-sm text-gray-600">Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projectStats as $stat)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-2 px-3 font-medium">
                                {{ $stat->project->name ?? '—' }}
                            </td>
                            <td class="py-2 px-3 text-green-600 font-semibold">
                                RWF{{ number_format($stat->total_paid, 2) }}
                            </td>
                            <td class="py-2 px-3 text-red-600 font-semibold">
                                RWF{{ number_format($stat->total_remaining, 2) }}
                            </td>
                            <td class="py-2 px-3 font-semibold">
                                RWF {{ number_format($stat->total_amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Income Records</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('incomes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add New Income</a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="py-2 px-4 border">ID</th>
                    <th class="py-2 px-4 border">Project</th>
                    <th class="py-2 px-4 border">Invoice #</th>
                    <th class="py-2 px-4 border">Amount Received</th>
                    <th class="py-2 px-4 border">Payment Status</th>
                    <th class="py-2 px-4 border">Amount Remaining</th>
                    <th class="py-2 px-4 border">Received At</th>
                    <th class="py-2 px-4 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $income)
                    <tr class="text-gray-700 text-center">
                        <td class="py-2 px-4 border">{{ $income->id }}</td>
                        <td class="py-2 px-4 border">{{ $income->project->name ?? 'N/A' }}</td>
                        <td class="py-2 px-4 border">{{ $income->invoice_number }}</td>
                        <td class="py-2 px-4 border">RWF {{ number_format($income->amount_received, 2) }}</td>
                        <td class="py-2 px-4 border">{{ $income->payment_status }}</td>
                        <td class="py-2 px-4 border">RWF {{ number_format($income->amount_remaining, 2) }}</td>
                        <td class="py-2 px-4 border">{{ $income->received_at->format('Y-m-d') }}</td>
                        <td class="py-2 px-4 border">
                            <a href="{{ route('incomes.show', $income->id) }}" class="text-blue-500 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-4 text-center">No income records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $incomes->links() }}
    </div>
</div>
@endsection
