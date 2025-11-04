@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-edit me-2 text-warning"></i>
                    Edit Employee
                </h2>
                <p class="text-muted mb-0">Update employee information</p>
            </div>
            <a href="{{ route('employees.index') }}" class="enhanced-button-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Employees
            </a>
        </div>
    </div>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-enhanced">
                <form action="{{ route('employees.update', $employee->id) }}" method="POST" id="employeeForm">
                    @csrf
                    @method('PUT')

                    {{-- Personal Information Section --}}
                    <div class="mb-4">
                        <h5 class="fw-bold text-primary mb-3">
                            <i class="fas fa-id-card me-2"></i>Personal Information
                        </h5>
                        <div class="row g-3">
                            <!-- First Name -->
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-semibold">
                                    <i class="fas fa-user me-1 text-muted"></i>
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="first_name" 
                                       id="first_name" 
                                       value="{{ old('first_name', $employee->first_name) }}"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-semibold">
                                    <i class="fas fa-user me-1 text-muted"></i>
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="last_name" 
                                       id="last_name" 
                                       value="{{ old('last_name', $employee->last_name) }}"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-1 text-muted"></i>
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-at"></i>
                                    </span>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email', $employee->email) }}"
                                           class="form-control @error('email') is-invalid @enderror"
                                           required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    <i class="fas fa-phone me-1 text-muted"></i>
                                    Phone Number
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-mobile-alt"></i>
                                    </span>
                                    <input type="text" 
                                           name="phone" 
                                           id="phone" 
                                           value="{{ old('phone', $employee->phone) }}"
                                           class="form-control @error('phone') is-invalid @enderror">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Employment Details Section --}}
                    <div class="mb-4">
                        <h5 class="fw-bold text-success mb-3">
                            <i class="fas fa-briefcase me-2"></i>Employment Details
                        </h5>
                        <div class="row g-3">
                            <!-- Position -->
                            <div class="col-md-6">
                                <label for="position" class="form-label fw-semibold">
                                    <i class="fas fa-tag me-1 text-muted"></i>
                                    Position / Job Title
                                </label>
                                <input type="text" 
                                       name="position" 
                                       id="position" 
                                       value="{{ old('position', $employee->position) }}"
                                       class="form-control @error('position') is-invalid @enderror">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="col-md-6">
                                <label for="department" class="form-label fw-semibold">
                                    <i class="fas fa-building me-1 text-muted"></i>
                                    Department
                                </label>
                                <select name="department" 
                                        id="department" 
                                        class="form-select @error('department') is-invalid @enderror">
                                    <option value="">Select department...</option>
                                    <option value="Engineering" {{ old('department', $employee->department) == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                    <option value="Finance" {{ old('department', $employee->department) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="HR" {{ old('department', $employee->department) == 'HR' ? 'selected' : '' }}>Human Resources</option>
                                    <option value="Operations" {{ old('department', $employee->department) == 'Operations' ? 'selected' : '' }}>Operations</option>
                                    <option value="Sales" {{ old('department', $employee->department) == 'Sales' ? 'selected' : '' }}>Sales</option>
                                    <option value="Marketing" {{ old('department', $employee->department) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="IT" {{ old('department', $employee->department) == 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="Administration" {{ old('department', $employee->department) == 'Administration' ? 'selected' : '' }}>Administration</option>
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date of Joining -->
                            <div class="col-md-6">
                                <label for="date_of_joining" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-check me-1 text-muted"></i>
                                    Date of Joining
                                </label>
                                <input type="date" 
                                       name="date_of_joining" 
                                       id="date_of_joining" 
                                       value="{{ old('date_of_joining', $employee->date_of_joining ? $employee->date_of_joining->format('Y-m-d') : '') }}"
                                       class="form-control @error('date_of_joining') is-invalid @enderror">
                                @error('date_of_joining')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Salary -->
                            <div class="col-md-6">
                                <label for="salary" class="form-label fw-semibold">
                                    <i class="fas fa-money-bill-wave me-1 text-muted"></i>
                                    Monthly Salary
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">RWF</span>
                                    <input type="number" 
                                           step="0.01" 
                                           name="salary" 
                                           id="salary" 
                                           value="{{ old('salary', $employee->salary) }}"
                                           class="form-control @error('salary') is-invalid @enderror">
                                </div>
                                @error('salary')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('employees.index') }}" class="btn btn-lg btn-light">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="enhanced-button-primary btn-lg">
                            <i class="fas fa-save me-2"></i> Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format salary input
        const salaryInput = document.getElementById('salary');
        if (salaryInput) {
            salaryInput.addEventListener('blur', function() {
                if (this.value) {
                    const value = parseFloat(this.value);
                    if (!isNaN(value)) {
                        this.value = value.toFixed(2);
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
