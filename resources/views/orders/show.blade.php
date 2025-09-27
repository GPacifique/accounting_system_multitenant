@extends('layouts.app')

@section('title', 'Order #' . ($order->id ?? ''))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h2>Order #{{ $order->id }}</h2>
            <div class="muted-small">Placed: {{ optional($order->created_at)->format('Y-m-d H:i') }}</div>
            <div class="mt-2"><strong>Customer:</strong> {{ $order->customer_name ?? '—' }} <small class="text-muted">{{ $order->customer_email ?? '' }}</small></div>
            <div><strong>Status:</strong> <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : ($order->status === 'cancelled' ? 'danger' : 'secondary')) }}">{{ ucfirst($order->status ?? '—') }}</span></div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Back</a>
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning">Edit</a>

            <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Delete this order?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    <!-- Order details & items -->
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Items</h6>

                    <table class="table table-sm">
                        <thead>
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
                                            <button class="btn btn-sm btn-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No items added.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <strong>Notes:</strong>
                            <div class="muted-small">{{ $order->notes ?? '—' }}</div>
                        </div>

                        <div class="text-end">
                            <div class="h5 mb-0">Total: <strong>{{ number_format($order->total ?? ($order->items->sum('line_total') ?? 0), 2) }}</strong></div>
                            <small class="text-muted">Items: {{ $order->items->count() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add item form -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6>Add Item</h6>
                    <form action="{{ route('orders.items.add', $order) }}" method="POST" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label small">Product name</label>
                            <input name="product_name" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Qty</label>
                            <input name="quantity" type="number" min="1" value="1" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Unit price</label>
                            <input name="unit_price" type="number" step="0.01" value="0.00" class="form-control" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-primary w-100">Add</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payments list -->
            <div class="card">
                <div class="card-body">
                    <h6>Payments</h6>
                    <ul class="list-group list-group-flush">
                        @if($order->payments && $order->payments->count())
                            @foreach($order->payments as $p)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ number_format($p->amount, 2) }}</strong>
                                        <div class="muted-small">{{ $p->method ?? '—' }} • {{ $p->reference ?? '' }}</div>
                                    </div>
                                    <div class="muted-small">{{ optional($p->created_at)->format('Y-m-d') }}</div>
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item text-center text-muted">No payments recorded.</li>
                        @endif
                    </ul>

                    <!-- Record payment -->
                    <div class="mt-3">
                        <h6 class="mb-2">Record Payment</h6>
                        <form action="{{ route('orders.pay', $order) }}" method="POST" class="row g-2">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label small">Amount</label>
                                <input name="amount" type="number" step="0.01" class="form-control" value="{{ $order->total ?? 0 }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Method</label>
                                <input name="method" class="form-control" value="cash" required>
                            </div>
                            <div class="col-md-4 text-end align-self-end">
                                <button class="btn btn-success w-100">Record Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right column: summary & actions -->
        <div class="col-lg-4">
            <div class="card mb-3 p-3">
                <h6>Summary</h6>
                <div class="d-flex justify-content-between">
                    <div class="muted-small">Subtotal</div>
                    <div><strong>{{ number_format($order->items->sum('line_total') ?? 0, 2) }}</strong></div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <div class="muted-small">Paid</div>
                    <div><strong>{{ number_format($order->payments->sum('amount') ?? 0, 2) }}</strong></div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <div class="muted-small">Balance</div>
                    <div><strong>{{ number_format( ($order->total ?? $order->items->sum('line_total') ?? 0) - ($order->payments->sum('amount') ?? 0), 2) }}</strong></div>
                </div>
