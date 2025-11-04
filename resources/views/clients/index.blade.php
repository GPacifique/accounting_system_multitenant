@extends('layouts.app')

@section('title', 'Clients')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="max-w-6xl mx-auto p-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold">Clients</h1>
            <p class="text-sm theme-aware-text-muted mt-1">Manage your customers and contacts</p>
        </div>

        <div class="flex items-center gap-3">
            <form id="clientsSearchForm" action="{{ route('clients.index') }}" method="GET" class="flex items-center gap-2">
                <label for="q" class="sr-only">Search clients</label>
                <div class="relative">
                    <input id="q" name="q" type="search" value="{{ request('q') ?? '' }}"
                        placeholder="Search name, contact person, phone or email..."
                        class="pl-10 pr-4 py-2 rounded-lg border theme-aware-bg-card shadow-sm focus:ring-2 focus:ring-indigo-200 focus:outline-none w-64"
                        autocomplete="off" aria-label="Search clients">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 theme-aware-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm">Search</button>
                <a href="{{ route('clients.index') }}" class="px-3 py-2 border rounded-lg theme-aware-text-secondary hover:bg-gray-50">Reset</a>
            </form>

            <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
                + New Client
            </a>

            <button id="exportCsvBtn" class="px-3 py-2 border rounded-lg text-gray-700 hover:bg-gray-50" title="Export visible clients to CSV">
                Export CSV
            </button>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Empty state --}}
    @if($clients->isEmpty())
        <div class="p-6 bg-yellow-50 border border-yellow-200 rounded text-center">
            <p class="text-gray-700">No clients found.</p>
            <a href="{{ route('clients.create') }}" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded">Create first client</a>
        </div>
    @else
        {{-- Table --}}
        <div class="overflow-x-auto theme-aware-bg-card rounded shadow-sm border">
            <table class="w-full table-auto min-w-[720px]">
                <thead>
                    <tr class="text-left bg-gray-50 text-sm">
                        <th class="px-4 py-3 w-12">#</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Contact Person</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($clients as $client)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-4 py-3 align-middle">{{ $client->id }}</td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    {{-- small avatar initials --}}
                                    <div class="w-10 h-10 rounded-md bg-indigo-50 text-indigo-700 flex items-center justify-center font-semibold text-sm">
                                        {{ strtoupper(substr($client->name ?? '', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $client->name }}</div>
                                        @if(!empty($client->address))
                                            <div class="text-xs theme-aware-text-muted">{{ Str::limit($client->address, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div class="font-medium">{{ $client->contact_person ?? '—' }}</div>
                                @if(!empty($client->contact_title))
                                    <div class="text-xs theme-aware-text-muted">{{ $client->contact_title }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-3 align-middle text-sm theme-aware-text-secondary">{{ $client->email ?? '—' }}</td>

                            <td class="px-4 py-3 align-middle text-sm theme-aware-text-secondary">{{ $client->phone ?? '—' }}</td>

                            <td class="px-4 py-3 align-middle text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('clients.show', $client) }}" class="text-indigo-600 hover:underline text-sm">View</a>
                                    <a href="{{ route('clients.edit', $client) }}" class="text-yellow-600 hover:underline text-sm">Edit</a>

                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline-block delete-client-form" data-name="{{ $client->name }}" onsubmit="return false;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:underline text-sm btn-delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm theme-aware-text-muted">Showing {{ $clients->firstItem() ?? 0 }} to {{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }}</div>
            <div>{{ $clients->withQueryString()->links() }}</div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* Small, focused tweaks to complement Tailwind */
.shadow-sm { box-shadow: 0 6px 18px rgba(20,24,40,0.06); }
.border { border: 1px solid rgba(17,24,39,0.04); }
.table-auto th, .table-auto td { vertical-align: middle; }
.btn-delete { cursor: pointer; }
@media (max-width: 640px) {
    #q { width: 100% !important; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Debounced search: if user clears query, auto-submit to reset quickly
    (function () {
        const input = document.getElementById('q');
        if (!input) return;
        let timer = null;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(() => {
                if (input.value.trim() === '') {
                    document.getElementById('clientsSearchForm').submit();
                }
            }, 650);
        });
    })();

    // Delete confirmation (delegated)
    document.querySelectorAll('.delete-client-form').forEach(form => {
        const btn = form.querySelector('.btn-delete');
        if (!btn) return;
        btn.addEventListener('click', function () {
            const name = form.dataset.name || 'this client';
            if (!confirm(`Delete ${name}? This action cannot be undone.`)) return;
            // create a real submit — this avoids immediate form submission when user cancels
            form.removeEventListener('submit', preventDefaultFn);
            form.submit();
        });
        function preventDefaultFn(e) { e.preventDefault(); }
        form.addEventListener('submit', preventDefaultFn);
    });

    // Export visible table rows to CSV (simple, client-side)
    document.getElementById('exportCsvBtn')?.addEventListener('click', function () {
        const rows = Array.from(document.querySelectorAll('table tbody tr'));
        if (!rows.length) { alert('No clients to export'); return; }

        const data = [];
        // header
        data.push(['ID','Name','Contact Person','Email','Phone']);

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 6) return;
            const id = cells[0].innerText.trim();
            const name = cells[1].querySelector('.font-medium')?.innerText.trim() ?? cells[1].innerText.trim();
            const contact = cells[2].querySelector('.font-medium')?.innerText.trim() ?? cells[2].innerText.trim();
            const email = cells[3].innerText.trim();
            const phone = cells[4].innerText.trim();
            data.push([id, name, contact, email, phone]);
        });

        const csvContent = data.map(r => r.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `clients-${new Date().toISOString().slice(0,10)}.csv`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    });
});
</script>
@endpush
