@extends('layouts.app')

@section('title', 'Edit Client')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold">Edit Client</h1>
            <p class="text-sm theme-aware-text-muted mt-1">Update client contact details — changes will be used on invoices and projects.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('clients.index') }}" class="text-sm px-3 py-2 border rounded-lg hover:theme-aware-bg-secondary">← Back to list</a>
        </div>
    </div>

    {{-- Validation summary --}}
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
            <div class="font-semibold text-sm text-red-700">Please fix the following:</div>
            <ul class="list-disc pl-5 mt-2 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="editClientForm" action="{{ route('clients.update', $client) }}" method="POST" class="theme-aware-bg-card rounded-lg shadow-sm p-6 needs-validation" novalidate>
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium mb-1">Name <span class="text-red-600">*</span></label>
                <input id="name" name="name" value="{{ old('name', $client->name) }}" required
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('name') border-red-400 @enderror"
                    placeholder="Company or organization name" aria-describedby="nameHelp">
                <div id="nameHelp" class="text-xs theme-aware-text-muted mt-1">Official client name (used on invoices).</div>
                @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Contact person --}}
            <div>
                <label for="contact_person" class="block text-sm font-medium mb-1">Contact Person <span class="text-red-600">*</span></label>
                <input id="contact_person" name="contact_person" value="{{ old('contact_person', $client->contact_person) }}" required
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('contact_person') border-red-400 @enderror"
                    placeholder="Full name of primary contact">
                @error('contact_person') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $client->email) }}"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('email') border-red-400 @enderror"
                    placeholder="contact@company.com">
                @error('email') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-sm font-medium mb-1">Phone <span class="theme-aware-text-muted text-xs">(preferred)</span></label>
                <div class="relative">
                    <input id="phone" name="phone" value="{{ old('phone', $client->phone) }}"
                        class="w-full border rounded-lg px-3 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('phone') border-red-400 @enderror"
                        placeholder="+250 78 123 4567" aria-label="phone">
                    <span class="absolute left-3 top-2.5 text-sm theme-aware-text-muted">📞</span>
                </div>
                @error('phone') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Address --}}
        <div class="mt-4">
            <label for="address" class="block text-sm font-medium mb-1">Address</label>
            <input id="address" name="address" value="{{ old('address', $client->address) }}"
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('address') border-red-400 @enderror"
                placeholder="Street, city, country">
            @error('address') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Notes --}}
        <div class="mt-4">
            <label for="notes" class="block text-sm font-medium mb-1">Notes</label>
            <textarea id="notes" name="notes" rows="3"
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-100 @error('notes') border-red-400 @enderror"
                placeholder="Billing instructions, special terms...">{{ old('notes', $client->notes) }}</textarea>
            @error('notes') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
            <a href="{{ route('clients.index') }}" class="px-4 py-2 rounded-lg border theme-aware-text-secondary hover:theme-aware-bg-secondary">Cancel</a>

            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow">Update Client</button>

            {{-- Optional: Quick delete (keeps safe confirmation) --}}
            <form id="deleteClientForm" action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return false;" class="inline-block">
                @csrf
                @method('DELETE')
                <button id="btnDeleteClient" type="button" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm">Delete</button>
            </form>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    /* Small focused tweaks to match the app's polished look */
    .shadow-sm { box-shadow: 0 8px 24px rgba(20,24,40,0.06); }
    .needs-validation:invalid { border-color: transparent; }
    .rounded-lg { border-radius: 10px; }
    .btn-delete { cursor: pointer; }
    @media (max-width: 640px) {
        .max-w-3xl { padding-left: 1rem; padding-right: 1rem; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // HTML5 validation UX
    const form = document.getElementById('editClientForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) firstInvalid.focus();
            }
            form.classList.add('was-validated');
        }, false);
    }

    // Tidy phone input on blur (keep digits, plus and spaces)
    const phone = document.getElementById('phone');
    if (phone) {
        phone.addEventListener('blur', function () {
            let v = this.value || '';
            v = v.replace(/[^\d+ ]/g, ''); // allow digits, plus and spaces
            if (v.startsWith('+') && !v.slice(1).includes(' ') && v.length > 4) {
                v = v.slice(0, 3) + ' ' + v.slice(3);
            }
            this.value = v.trim();
        });
    }

    // Delete confirmation — submit only if confirmed
    const deleteForm = document.getElementById('deleteClientForm');
    const deleteBtn = document.getElementById('btnDeleteClient');
    if (deleteForm && deleteBtn) {
        deleteBtn.addEventListener('click', function () {
            const name = "{{ addslashes($client->name ?? 'this client') }}";
            if (!confirm(`Delete ${name}? This action cannot be undone.`)) return;
            // remove onsubmit prevent and submit
            deleteForm.onsubmit = null;
            deleteForm.submit();
        });
    }
});
</script>
@endpush
