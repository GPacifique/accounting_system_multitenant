@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fa-solid fa-boxes-stacked me-2"></i> Orders</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> New Order
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary" title="Reset filters">
                <i class="fa-solid fa-arrows-rotate"></i>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Customer name, order id, product...">
                </div>

                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label small">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>

                <div class="col-md-2 text-end">
                    <button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flash -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Orders table -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>
                                <strong>{{ $order->customer_name ?? '—' }}</strong><br>
                                <small class="text-muted">{{ $order->customer_email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $order->items_count ?? $order->items->count() ?? 0 }}</span>
                            </td>
                            <td>{{ number_format($order->total ?? ($order->items->sum('line_total') ?? 0), 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : ($order->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($order->status ?? '—') }}
                                </span>
                            </td>
                            <td>{{ optional($order->created_at)->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this order?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>

                                <!-- Quick actions: add item / mark paid -->
                                <a href="{{ route('orders.items.add', $order) }}" class="btn btn-sm btn-outline-primary" title="Add item">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-success" title="Mark paid" data-bs-toggle="modal" data-bs-target="#payModal-{{ $order->id }}">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </a>

                                <!-- Payment modal (simple) -->
                                <div class="modal fade" id="payModal-{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form action="{{ route('orders.pay', $order) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Record Payment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-2">
                                                        <label class="form-label">Amount</label>
                                                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ $order->total ?? 0 }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Method</label>
                                                        <input type="text" name="method" class="form-control" value="cash">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Reference</label>
                                                        <input type="text" name="reference" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                                                    <button class="btn btn-success" type="submit">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- /modal -->
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">Showing {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() ?? 0 }}</div>
                <div>{{ $orders->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
