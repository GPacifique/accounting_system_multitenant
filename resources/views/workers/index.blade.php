@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="page-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Workers</h1>
            <div class="text-muted small">Manage your employees — view, edit or remove workers</div>
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

            <a href="{{ route('workers.create') }}" class="btn btn-sm btn-primary ms-2">New Worker</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:56px">#</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th style="min-width:130px">Salary</th>
                            <th>Hired</th>
                            <th style="width:180px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workers as $worker)
                            <tr>
                                <td class="align-middle">
                                    <div class="avatar-sm rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <span class="fw-bold small">
                                            {{ strtoupper(substr($worker->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($worker->last_name ?? '', 0, 1)) }}
                                        </span>
                                    </div>
                                </td>

                                <td class="align-middle">
                                    <div class="fw-semibold">{{ $worker->full_name ?? "{$worker->first_name} {$worker->last_name}" }}</div>
                                    @if(!empty($worker->email))
                                        <div class="small text-muted">{{ $worker->email }}</div>
                                    @endif
                                </td>

                                <td class="align-middle">
                                    <span class="badge bg-light text-dark">{{ $worker->position }}</span>
                                </td>

                                <td class="align-middle">
                                    <div class="fw-medium">
                                        {{ number_format((($worker->salary_cents ?? 0) / 100), 2) }} {{ $worker->currency ?? '' }}
                                    </div>
                                    <div class="small text-muted">Gross</div>
                                </td>

                                <td class="align-middle">
                                    {{ optional($worker->hired_at)->format('Y-m-d') ?? '-' }}
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
                                <td colspan="6" class="text-center py-4 small text-muted">No workers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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

    /* Small responsive tweaks */
    @media (max-width: 575.98px) {
        .page-header { gap: .5rem; }
        .input-group { width: 100%; }
    }
</style>
@endpush
@endsection
