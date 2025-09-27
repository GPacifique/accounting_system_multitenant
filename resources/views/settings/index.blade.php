@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Application Settings</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">App Name</label>
            <input type="text" name="app_name" class="form-control" 
                   value="{{ old('app_name', $settings['app_name'] ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Company Email</label>
            <input type="email" name="company_email" class="form-control" 
                   value="{{ old('company_email', $settings['company_email'] ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Tax Rate (%)</label>
            <input type="number" step="0.01" name="tax_rate" class="form-control" 
                   value="{{ old('tax_rate', $settings['tax_rate'] ?? '') }}">
        </div>

        <button type="submit" class="btn btn-success">Save Settings</button>
    </form>
</div>
@endsection
