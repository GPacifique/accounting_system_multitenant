@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-gradient p-4">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h2 class="h4 mb-1 text-white">Edit Worker</h2>
                            <div class="text-white-50 small">Update worker details — changes affect payroll and records.</div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('workers.show', $worker) }}" class="btn btn-outline-light btn-sm align-self-start">← Back to profile</a>
                            <a href="{{ route('workers.index') }}" class="btn btn-outline-light btn-sm align-self-start">List</a>
                        </div>
                    </div>
                </div>

                <form action="{{ route('workers.update', $worker) }}" method="POST" class="card-body p-4 needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- names --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-medium">First name <span class="text-danger">*</span></label>
                            <input id="first_name" type="text" name="first_name"
                                   class="form-control form-control-lg @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $worker->first_name) }}" required
                                   placeholder="e.g. Jane" aria-describedby="firstNameHelp" >
                            <div id="firstNameHelp" class="form-text small">Given name only.</div>
                            @error('first_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-medium">Last name <span class="text-danger">*</span></label>
                            <input id="last_name" type="text" name="last_name"
                                   class="form-control form-control-lg @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $worker->last_name) }}" required
                                   placeholder="e.g. Doe">
                            @error('last_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- contact --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-medium">Email</label>
                            <input id="email" type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $worker->email) }}" placeholder="name@company.com">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-medium">Phone</label>
                            <div class="input-group">
                                <span class="input-group-text">📞</span>
                                <input id="phone" type="tel" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $worker->phone) }}" placeholder="+250 78 123 4567" aria-label="phone">
                            </div>
                            <div class="form-text small">International format preferred.</div>
                            @error('phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- position / status --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="position" class="form-label fw-medium">Position</label>
                            <input id="position" type="text" name="position"
                                   class="form-control @error('position') is-invalid @enderror"
                                   value="{{ old('position', $worker->position) }}" placeholder="e.g. Site Manager">
                            @error('position')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label fw-medium">Status</label>
                            @php $st = old('status', $worker->status ?? 'active'); @endphp
                            <select id="status" name="status" class="form-select">
                                <option value="active" {{ $st === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="probation" {{ $st === 'probation' ? 'selected' : '' }}>Probation</option>
                                <option value="inactive" {{ $st === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="terminated" {{ $st === 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                            <div class="form-text small">Choose the employment status.</div>
                        </div>
                    </div>

                    {{-- salary / currency --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="salary" class="form-label fw-medium">Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">💰</span>
                                <input id="salary" type="number" step="0.01" min="0" name="salary"
                                       class="form-control form-control-lg @error('salary') is-invalid @enderror"
                                       value="{{ old('salary', (isset($worker->salary_cents) ? ($worker->salary_cents / 100) : '')) }}"
                                       placeholder="0.00" aria-label="salary">
                                <span class="input-group-text">/ month</span>
                            </div>
                            <div class="form-text small">Enter gross salary (two decimals allowed).</div>
                            @error('salary')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="currency" class="form-label fw-medium">Currency</label>
                            <input id="currency" type="text" name="currency"
                                   class="form-control @error('currency') is-invalid @enderror"
                                   value="{{ old('currency', $worker->currency) }}" placeholder="USD">
                            @error('currency')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- hired_at / notes --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="hired_at" class="form-label fw-medium">Hired at</label>
                            <input id="hired_at" type="date" name="hired_at"
                                   class="form-control @error('hired_at') is-invalid @enderror"
                                   value="{{ old('hired_at', optional($worker->hired_at)->format('Y-m-d')) }}">
                            @error('hired_at')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="notes" class="form-label fw-medium">Notes</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Optional notes, department, on-boarding tasks...">{{ old('notes', $worker->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- actions --}}
                    <div class="d-flex gap-2 justify-content-end align-items-center">
                        <button type="submit" class="btn btn-lg btn-primary shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save me-1" viewBox="0 0 16 16">
                                <path d="M8 5.5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5.5z"/>
                                <path d="M6 0h4a2 2 0 0 1 2 2v3h-1V2a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v3H4V2a2 2 0 0 1 2-2z"/>
                                <path d="M1 5v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V5H1zm3 1h8v6H4V6z"/>
                            </svg>
                            Save
                        </button>

                        <a href="{{ route('workers.show', $worker) }}" class="btn btn-outline-secondary btn-lg">Cancel</a>

                        {{-- optional delete button (small) --}}
                        <form action="{{ route('workers.destroy', $worker) }}" method="POST" class="ms-2" onsubmit="return confirm('Delete this worker? This action is irreversible.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg">Delete</button>
                        </form>
                    </div>
                </form>

                {{-- validation summary --}}
                @if ($errors->any())
                    <div class="mt-3 px-4 pb-4">
                        <div class="alert alert-danger small mb-0">
                            <strong>There are {{ $errors->count() }} problem(s) with your submission.</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Match Create form visuals */
    .bg-gradient {
        background: linear-gradient(90deg, rgba(58,123,213,1) 0%, rgba(0,210,255,0.85) 100%);
    }

    .form-control, .form-select {
        border-radius: 10px;
        box-shadow: none;
        border: 1px solid rgba(22,28,36,0.08);
        padding: 0.65rem 0.75rem;
        transition: box-shadow .15s ease, transform .06s ease;
        background-clip: padding-box;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        box-shadow: 0 6px 18px rgba(13,110,253,0.08);
        transform: translateY(-1px);
        border-color: rgba(13,110,253,0.45);
    }

    .card { border-radius: 12px; }
    .card-body { background: #fff; }
    .input-group-text { border-radius: 8px; background: rgba(0,0,0,0.03); }
    .invalid-feedback { font-size: .85rem; }

    @media (max-width: 575.98px) {
        .card-header .d-flex { flex-direction: column; gap: .5rem; align-items: flex-start; }
        .btn-lg { width: 100%; }
        form .d-flex { flex-direction: column; gap: .5rem; }
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        'use strict';

        // format salary to 2 decimals on blur
        const salaryInput = document.getElementById('salary');
        if (salaryInput) {
            salaryInput.addEventListener('blur', function () {
                if (this.value === '') return;
                const v = parseFloat(this.value);
                if (!isNaN(v)) this.value = v.toFixed(2);
            });
            salaryInput.addEventListener('input', function () {
                if (this.value && parseFloat(this.value) < 0) this.value = 0;
            });
        }

        // tidy phone on blur (keep digits and plus)
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('blur', function () {
                let v = this.value || '';
                v = v.replace(/[^\d+]/g, '');
                if (v.length > 3 && v[0] === '+') {
                    v = v.slice(0,3) + ' ' + v.slice(3);
                }
                this.value = v;
            });
        }

        // HTML5 validation UX
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) firstInvalid.focus();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
@endpush
