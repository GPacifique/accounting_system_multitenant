@extends('layouts.app')

@section('title', 'Workers — Staff & Daily Payments | SiteLedger')
@section('meta_description', 'Manage workers, record daily payments, and review payment history by date and worker position in SiteLedger.')
@section('meta_keywords', 'workers, daily payments, payroll, construction, positions, staff management')

@section('content')
<div class="container my-5">
    {{-- Role Check: Admin or Manager Only --}}
    @unless(auth()->user()->hasAnyRole(['admin', 'manager']))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> You do not have permission to access this page.
        </div>
        @php
            abort(403, 'Unauthorized access');
        @endphp
    @endunless

    <div class="page-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Workers</h1>
            <div class="text-muted small">Manage your workers — view, edit or remove workers</div>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="{{ route('workers.index') }}" class="d-flex align-items-center gap-2">
                <div class="input-group">
                    <input
                        name="q"
                        value="{{ request('q') }}"
                        type="search"
                        class="form-control form-control-sm"
                        placeholder="Search name, position or email..."
                        aria-label="Search workers"
                    >
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Search</button>
                </div>
                <a href="{{ route('workers.index') }}" class="btn btn-sm btn-outline-info" title="Clear search">Clear</a>
            </form>

            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                {{-- Download Buttons --}}
                <x-download-buttons 
                    route="workers.export" 
                    filename="workers" 
                    size="sm" />
                
                <a href="{{ route('workers.create') }}" class="btn btn-sm btn-primary ms-2">New Worker</a>
            @endif
        </div>
    </div>

    {{-- Standalone bulk payments form (kept empty; inputs reference it via HTML form attribute to avoid nested forms) --}}
    <form id="bulkPaymentsForm" action="{{ route('workers.payments.bulk') }}" method="POST" class="d-none">
        @csrf
    </form>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="mb-3 d-flex flex-wrap align-items-end gap-2">
                <div>
                    <label for="paid_on" class="form-label small mb-1">Payment Date</label>
                    <input type="date" id="paid_on" name="paid_on" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}" required form="bulkPaymentsForm">
                </div>
                <div class="ms-auto small text-muted">Tick workers and enter amount(s), then click Save Payments.</div>
                <button type="submit" class="btn btn-sm btn-success" form="bulkPaymentsForm">Save Payments</button>
            </div>

            <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:36px">
                                    <input type="checkbox" id="checkAll" class="form-check-input" onclick="document.querySelectorAll('.chk-worker').forEach(cb=>cb.checked=this.checked)">
                                </th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Hired</th>
                                <th style="min-width:160px">Pay Amount</th>
                                <th style="width:180px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workers as $worker)
                                <tr>
                                    <td class="align-middle">
                                        <input type="checkbox" name="worker_ids[]" value="{{ $worker->id }}" class="form-check-input chk-worker" form="bulkPaymentsForm">
                                    </td>

                                    <td class="align-middle">
                                        <div class="fw-semibold">{{ $worker->full_name ?? "{$worker->first_name} {$worker->last_name}" }}</div>
                                        @if(!empty($worker->email))
                                            <div class="small text-muted">{{ $worker->email }}</div>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        <span class="badge bg-light text-success">{{ $worker->position }}</span>
                                    </td>

                                    <td class="align-middle">
                                        {{ optional($worker->hired_at)->format('Y-m-d') ?? '-' }}
                                    </td>

                                    <td class="align-middle">
                                        <div class="input-group input-group-sm" style="max-width:180px;">
                                            <span class="input-group-text">RWF</span>
                                            <input type="number" step="0.01" min="0" name="amounts[{{ $worker->id }}]" class="form-control" placeholder="0.00" form="bulkPaymentsForm">
                                        </div>
                                        <div class="form-text">Leave blank/0 to skip</div>
                                    </td>

                                    <td class="align-middle text-end">
                                        <a href="{{ route('workers.show', $worker) }}" class="btn btn-sm btn-outline-primary me-1">View</a>
                                        <a href="{{ route('workers.edit', $worker) }}" class="btn btn-sm btn-outline-secondary me-1">Edit</a>

                                        <form action="{{ route('workers.destroy', $worker) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this worker?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 small text-muted">No workers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>
        </div>
    </div>

    {{-- Daily Payments History --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header theme-aware-bg-card border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Daily Payments History</h2>
                <div class="text-muted small">
                    @if(!empty($firstPaymentDate))
                        From {{ \Illuminate\Support\Carbon::parse($firstPaymentDate)->format('Y-m-d') }} to {{ now()->format('Y-m-d') }}
                    @else
                        No payments recorded yet
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if(isset($payments) && $payments->count())
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Worker</th>
                                <th class="text-end" style="min-width:130px">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $p)
                                <tr>
                                    <td class="align-middle">{{ optional($p->paid_on)->format('Y-m-d') }}</td>
                                    <td class="align-middle">
                                        <a href="{{ route('workers.show', $p->worker_id) }}" class="text-decoration-none">
                                            {{ $p->worker->full_name ?? ($p->worker->first_name . ' ' . $p->worker->last_name) }}
                                        </a>
                                    </td>
                                    <td class="align-middle text-end fw-semibold text-success">
                                        RWF {{ number_format($p->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 d-flex justify-content-end">{{ $payments->links() }}</div>
            @else
                <div class="p-4 text-center text-muted small">No payments to display.</div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Showing {{ $workers->firstItem() ?? 0 }} to {{ $workers->lastItem() ?? 0 }} of {{ $workers->total() ?? 0 }}
        </div>

        <div>
            {{ $workers->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- Local view styles (you can move to app.css) --}}
@push('styles')
<style>
    /* Card/table aesthetics */
    .card { border: 0; border-radius: 12px; }
    .table thead th { border-bottom: 0; font-weight: 600; }
    .table tbody tr { border-bottom: 1px solid rgba(0,0,0,0.04); }
    .avatar-sm { width:42px; height:42px; font-size:0.9rem; border-radius:8px; }
    .btn-sm { padding: .35rem .5rem; font-size: .82rem; }
    .form-text { font-size: .72rem; }

    /* Small responsive tweaks */
    @media (max-width: 575.98px) {
        .page-header { gap: .5rem; }
        .input-group { width: 100%; }
    }
</style>
@endpush
@endsection
