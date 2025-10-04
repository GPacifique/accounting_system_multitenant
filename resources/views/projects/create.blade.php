{{-- resources/views/projects/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Project')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold">Create Project</h1>
            <p class="text-sm text-gray-500 mt-1">Create a project and link it to a client. Keep dates and budgets accurate.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('projects.index') }}" class="text-sm px-3 py-2 border rounded-lg hover:bg-gray-50">← Back to projects</a>
        </div>
    </div>

    {{-- Validation summary --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
            <strong class="block mb-2 text-red-700">Please fix the errors below:</strong>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li class="text-sm text-red-700">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="createProjectForm" action="{{ route('projects.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6 needs-validation" novalidate>
        @csrf

        {{-- Client select --}}
        <div class="mb-4">
            <label for="client_id" class="block text-sm font-medium mb-1">Client <span class="text-red-600">*</span></label>
            <select id="client_id" name="client_id" required
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('client_id') border-red-400 @enderror">
                <option value="">-- select client --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
            <div class="text-xs text-gray-400 mt-1">Pick the client this project is for.</div>
            @error('client_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Project name --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium mb-1">Project name <span class="text-red-600">*</span></label>
            <input id="name" name="name" value="{{ old('name') }}" required
                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('name') border-red-400 @enderror"
                   placeholder="e.g. Kigali Bridge Rehab">
            @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Dates --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="start_date" class="block text-sm font-medium mb-1">Start date</label>
                <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('start_date') border-red-400 @enderror">
                <div class="text-xs text-gray-400 mt-1">Optional — leave empty if TBD.</div>
                @error('start_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium mb-1">End date</label>
                <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('end_date') border-red-400 @enderror">
                <div id="dateHelp" class="text-xs text-gray-400 mt-1">Optional — must be same or after start date if provided.</div>
                @error('end_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Contract value --}}
        <div class="mb-4">
            <label for="contract_value" class="block text-sm font-medium mb-1">Contract Value</label>
            <div class="relative">
                <input id="contract_value" name="contract_value" type="number" step="0.01" min="0"
                       value="{{ old('contract_value', '') }}"
                       class="w-full border rounded-lg px-3 py-2 pr-28 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('contract_value') border-red-400 @enderror"
                       placeholder="0.00" aria-describedby="contractHelp">
                <select id="contract_currency" name="contract_currency" class="absolute right-2 top-1/2 -translate-y-1/2 border rounded px-2 py-1 text-sm bg-white">
                    @php $cur = old('contract_currency', 'USD'); @endphp
                    <option value="USD" {{ $cur === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="RWF" {{ $cur === 'RWF' ? 'selected' : '' }}>RWF</option>
                    <option value="EUR" {{ $cur === 'EUR' ? 'selected' : '' }}>EUR</option>
                </select>
            </div>
            <div id="contractHelp" class="text-xs text-gray-400 mt-1">Enter contract amount. You can change currency if needed.</div>
            @error('contract_value') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Amount paid --}}
        <div class="mb-4">
            <label for="amount_paid" class="block text-sm font-medium mb-1">Amount Paid</label>
            <div class="relative">
                <input id="amount_paid" name="amount_paid" type="number" step="0.01" min="0"
                       value="{{ old('amount_paid', '') }}"
                       class="w-full border rounded-lg px-3 py-2 pr-28 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('amount_paid') border-red-400 @enderror"
                       placeholder="0.00" aria-describedby="amountPaidHelp">
                <select id="amount_paid_currency" name="amount_paid_currency" class="absolute right-2 top-1/2 -translate-y-1/2 border rounded px-2 py-1 text-sm bg-white">
                    @php $paidCur = old('amount_paid_currency', $cur ?? 'USD'); @endphp
                    <option value="USD" {{ $paidCur === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="RWF" {{ $paidCur === 'RWF' ? 'selected' : '' }}>RWF</option>
                    <option value="EUR" {{ $paidCur === 'EUR' ? 'selected' : '' }}>EUR</option>
                </select>
            </div>
            <div id="amountPaidHelp" class="text-xs text-gray-400 mt-1">Enter amount already paid towards contract.</div>
            @error('amount_paid') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Amount remaining --}}
        <div class="mb-4">
            <label for="amount_remaining" class="block text-sm font-medium mb-1">Amount Remaining</label>
            <div class="relative">
                <input id="amount_remaining" name="amount_remaining" type="number" step="0.01" min="0"
                       value="{{ old('amount_remaining', '') }}"
                       class="w-full border rounded-lg px-3 py-2 pr-28 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('amount_remaining') border-red-400 @enderror"
                       placeholder="0.00" aria-describedby="amountRemainingHelp">
                <select id="amount_remaining_currency" name="amount_remaining_currency" class="absolute right-2 top-1/2 -translate-y-1/2 border rounded px-2 py-1 text-sm bg-white">
                    @php $remCur = old('amount_remaining_currency', $cur ?? 'USD'); @endphp
                    <option value="USD" {{ $remCur === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="RWF" {{ $remCur === 'RWF' ? 'selected' : '' }}>RWF</option>
                    <option value="EUR" {{ $remCur === 'EUR' ? 'selected' : '' }}>EUR</option>
                </select>
            </div>
            <div id="amountRemainingHelp" class="text-xs text-gray-400 mt-1">Enter amount still remaining to be paid towards contract.</div>
            @error('amount_remaining') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium mb-1">Project Status</label>
            <select id="status" name="status"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('status') border-red-400 @enderror">
                @php $status = old('status', 'planned'); @endphp
                <option value="planned" {{ $status === 'planned' ? 'selected' : '' }}>Planned</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="on-hold" {{ $status === 'on-hold' ? 'selected' : '' }}>On Hold</option>
            </select>
            <div class="text-xs text-gray-400 mt-1">Select the current status of the project.</div>
            @error('status') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Optional description / notes --}}
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium mb-1">Description / Notes</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('description') border-red-400 @enderror"
                      placeholder="Short description, scope or special terms...">{{ old('description') }}</textarea>
            @error('description') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
            <a href="{{ route('projects.index') }}" class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow">Save Project</button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    /* small tweaks to match app look */
    .shadow-sm { box-shadow: 0 8px 24px rgba(20,24,40,0.06); }
    .rounded-lg { border-radius: 10px; }
    select[name="contract_currency"], select[name="amount_paid_currency"], select[name="amount_remaining_currency"] { border: 1px solid rgba(17,24,39,0.06); }
    @media (max-width: 640px) {
        .max-w-3xl { padding-left: 1rem; padding-right: 1rem; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // HTML5 validation UX & date logic
    const form = document.getElementById('createProjectForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            // check date logic: if both dates provided, end >= start
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            if (start && end) {
                const s = new Date(start);
                const en = new Date(end);
                if (en < s) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('End date cannot be before start date.');
                    document.getElementById('end_date').focus();
                    return false;
                }
            }

            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) firstInvalid.focus();
            }
            form.classList.add('was-validated');
        }, false);
    }

    // contract value: format to 2 decimals on blur and prevent negative
    const contractInput = document.getElementById('contract_value');
    if (contractInput) {
        contractInput.addEventListener('blur', function () {
            if (this.value === '') return;
            const n = parseFloat(this.value);
            if (!isNaN(n)) this.value = n.toFixed(2);
        });
        contractInput.addEventListener('input', function () {
            if (this.value && parseFloat(this.value) < 0) this.value = 0;
        });
    }

    // auto-select first client if only one exists (placeholder + one option)
    const clientSelect = document.getElementById('client_id');
    if (clientSelect && clientSelect.options.length === 2 && !clientSelect.value) {
        clientSelect.selectedIndex = 1;
    }
});
</script>
@endpush
