@extends('layouts.app')

@section('title', 'Create Subscription')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.subscriptions.index') }}" 
           class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Create New Subscription</h1>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('admin.subscriptions.store') }}">
            @csrf

            <!-- Tenant Selection -->
            <div class="mb-6">
                <label for="tenant_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Tenant
                </label>
                <select name="tenant_id" id="tenant_id" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                        required>
                    <option value="">Select a tenant</option>
                    @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->name }} ({{ $tenant->domain }})
                    </option>
                    @endforeach
                </select>
                @error('tenant_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Plan Selection -->
            <div class="mb-6">
                <label for="plan" class="block text-sm font-medium text-gray-700 mb-2">
                    Plan
                </label>
                <select name="plan" id="plan" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                        required>
                    <option value="">Select a plan</option>
                    @foreach($plans as $key => $plan)
                    <option value="{{ $key }}" 
                            data-monthly="{{ $plan['monthly_price'] }}"
                            data-yearly="{{ $plan['yearly_price'] }}"
                            {{ old('plan') == $key ? 'selected' : '' }}>
                        {{ $plan['name'] }}
                    </option>
                    @endforeach
                </select>
                @error('plan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Billing Cycle -->
            <div class="mb-6">
                <label for="billing_cycle" class="block text-sm font-medium text-gray-700 mb-2">
                    Billing Cycle
                </label>
                <select name="billing_cycle" id="billing_cycle" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                        required>
                    <option value="">Select billing cycle</option>
                    <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>
                        Monthly
                    </option>
                    <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>
                        Yearly (Save 17%)
                    </option>
                </select>
                @error('billing_cycle')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Start Date -->
            <div class="mb-6">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Start Date
                </label>
                <input type="date" name="start_date" id="start_date" 
                       value="{{ old('start_date', now()->format('Y-m-d')) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       required>
                @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price Preview -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Price Preview</h3>
                <div id="price-preview" class="text-lg font-semibold text-gray-900">
                    Select a plan and billing cycle to see pricing
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.subscriptions.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Create Subscription
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan');
    const billingCycleSelect = document.getElementById('billing_cycle');
    const pricePreview = document.getElementById('price-preview');

    function updatePricePreview() {
        const selectedPlan = planSelect.selectedOptions[0];
        const billingCycle = billingCycleSelect.value;

        if (selectedPlan && selectedPlan.value && billingCycle) {
            const price = billingCycle === 'monthly' 
                ? selectedPlan.dataset.monthly 
                : selectedPlan.dataset.yearly;
            
            const planName = selectedPlan.textContent;
            const cycleText = billingCycle === 'monthly' ? 'per month' : 'per year';
            
            pricePreview.innerHTML = `
                <div class="text-lg font-semibold text-blue-600">
                    $${parseFloat(price).toFixed(2)} ${cycleText}
                </div>
                <div class="text-sm text-gray-600">${planName} Plan</div>
            `;
        } else {
            pricePreview.textContent = 'Select a plan and billing cycle to see pricing';
        }
    }

    planSelect.addEventListener('change', updatePricePreview);
    billingCycleSelect.addEventListener('change', updatePricePreview);

    // Update on page load if values are selected
    updatePricePreview();
});
</script>
@endsection