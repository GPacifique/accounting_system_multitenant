@extends('layouts.app')

@section('title', 'Order #' . ($order->id ?? ''))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-6">
        <div>
            <h2 class="text-3xl font-bold theme-aware-text leading-tight">Order #{{ $order->id }}</h2>
            <div class="text-sm theme-aware-text-muted">Placed: {{ optional($order->created_at)->format('Y-m-d H:i') }}</div>
            <div class="mt-2 text-sm theme-aware-text-secondary"><strong>Customer:</strong> {{ $order->customer_name ?? '—' }} <span class="theme-aware-text-muted">{{ $order->customer_email ?? '' }}</span></div>
            <div class="mt-1 text-sm theme-aware-text-secondary"><strong>Status:</strong>
                @php
                    $status = $order->status ?? '—';
                    $statusClasses = $status === 'completed'
                        ? 'bg-green-100 text-green-800'
                        : ($status === 'processing'
                            ? 'bg-blue-100 text-blue-800'
                            : ($status === 'cancelled' ? 'bg-red-100 text-red-800' : 'theme-aware-bg-secondary theme-aware-text'));
                @endphp
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClasses }}">{{ ucfirst($status) }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2 mt-4 md:mt-0">
            <a href="{{ route('orders.edit', $order) }}" class="btn-primary">Edit</a>
            <a href="{{ route('orders.index') }}" class="btn-secondary">Back</a>
            <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Delete this order?')">
                @csrf
                @method('DELETE')
                <button class="btn-danger">Delete</button>
            </form>
        </div>
    </div>

    <!-- Order details & items -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden mb-3">
                <div class="p-6">
                    <h6 class="mb-3">Items</h6>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="theme-aware-bg-secondary">
                            <tr>
                                <th>Product</th>
                                <th style="width:120px">Qty</th>
                                <th style="width:140px">Unit</th>
                                <th style="width:140px">Line</th>
                                <th style="width:110px" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->line_total, 2) }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('orders.items.remove', [$order, $item]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-sm theme-aware-text-muted py-6">No items added.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4">
                        <div>
                            <h3 class="text-sm font-medium theme-aware-text-muted">Notes</h3>
                            <p class="mt-1 text-sm theme-aware-text">{{ $order->notes ?? '—' }}</p>
                        </div>

                        <div class="text-right mt-4 sm:mt-0">
                            <div class="text-xl font-bold theme-aware-text">Total: {{ number_format($order->total ?? ($order->items->sum('line_total') ?? 0), 2) }}</div>
                            <div class="text-xs theme-aware-text-muted">Items: {{ $order->items->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add item form -->
            <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden mb-3">
                <div class="p-6">
                    <h6>Add Item</h6>
                    <form action="{{ route('orders.items.add', $order) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @csrf
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium theme-aware-text-secondary">Product name</label>
                            <input name="product_name" class="mt-1 w-full rounded-md theme-aware-border focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium theme-aware-text-secondary">Qty</label>
                            <input name="quantity" type="number" min="1" value="1" class="mt-1 w-full rounded-md theme-aware-border focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium theme-aware-text-secondary">Unit price</label>
                            <input name="unit_price" type="number" step="0.01" value="0.00" class="mt-1 w-full rounded-md theme-aware-border focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div class="md:col-span-4 text-right">
                            <button class="btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payments list -->
            <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h6>Payments</h6>
                    <ul class="divide-y divide-gray-200">
                        @if($order->payments && $order->payments->count())
                            @foreach($order->payments as $p)
                                <li class="py-3 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-semibold theme-aware-text">{{ number_format($p->amount, 2) }}</div>
                                        <div class="text-xs theme-aware-text-muted">{{ $p->method ?? '—' }} • {{ $p->reference ?? '' }}</div>
                                    </div>
                                    <div class="text-xs theme-aware-text-muted">{{ optional($p->created_at)->format('Y-m-d') }}</div>
                                </li>
                            @endforeach
                        @else
                            <li class="py-6 text-center text-sm theme-aware-text-muted">No payments recorded.</li>
                        @endif
                    </ul>

                    <!-- Record payment -->
                    <div class="mt-3">
                        <h6 class="mb-2">Record Payment</h6>
                        <form action="{{ route('orders.pay', $order) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium theme-aware-text-secondary">Amount</label>
                                <input name="amount" type="number" step="0.01" class="mt-1 w-full rounded-md theme-aware-border focus:border-indigo-500 focus:ring-indigo-500" value="{{ $order->total ?? 0 }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium theme-aware-text-secondary">Method</label>
                                <input name="method" class="mt-1 w-full rounded-md theme-aware-border focus:border-indigo-500 focus:ring-indigo-500" value="cash" required>
                            </div>
                            <div class="md:self-end text-right">
                                <button class="btn-success">Record Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right column: summary & actions -->
        <div class="lg:col-span-1">
            <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden p-6">
                <h6 class="text-lg font-semibold theme-aware-text-secondary">Summary</h6>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="theme-aware-text-muted">Subtotal</span>
                        <span class="font-semibold theme-aware-text">{{ number_format($order->items->sum('line_total') ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="theme-aware-text-muted">Paid</span>
                        <span class="font-semibold theme-aware-text">{{ number_format($order->payments->sum('amount') ?? 0, 2) }}</span>
                    </div>
                    <hr>
                    <div class="flex items-center justify-between text-base">
                        <span class="theme-aware-text-secondary">Balance</span>
                        <span class="font-bold theme-aware-text">{{ number_format( ($order->total ?? $order->items->sum('line_total') ?? 0) - ($order->payments->sum('amount') ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 theme-aware-bg-tertiary border theme-aware-border rounded-md font-semibold text-xs theme-aware-text-secondary uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:theme-aware-border-secondary focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-danger {
        @apply inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-success {
        @apply inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
</style>
@endpush
