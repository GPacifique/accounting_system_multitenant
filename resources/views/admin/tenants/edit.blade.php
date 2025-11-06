{{-- resources/views/admin/tenants/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Tenant: ' . $tenant->name . ' - System Administration | SiteLedger')
@section('meta_description', 'Edit tenant settings, subscription plan, and business information for ' . $tenant->name . ' in the SiteLedger multitenant accounting system.')
@section('meta_keywords', 'edit tenant, tenant management, business settings, subscription management, tenant administration')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-purple-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center">
                    <div class="bg-white/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-edit text-2xl"></i>
                    </div>
                    Edit Tenant: {{ $tenant->name }}
                </h1>
                <p class="text-blue-100 mt-2">Update business information and settings</p>
                <div class="flex items-center mt-3 space-x-4 text-sm">
                    <span class="bg-white/20 px-2 py-1 rounded">{{ $tenant->domain }}</span>
                    <span class="bg-white/20 px-2 py-1 rounded">{{ ucfirst($tenant->status) }}</span>
                    <span class="bg-white/20 px-2 py-1 rounded">{{ $tenant->users_count ?? 0 }} Users</span>
                </div>
            </div>
            <a href="{{ route('admin.tenants.show', $tenant) }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                <i class="fas fa-eye mr-2"></i>
                View Details
            </a>
        </div>
    </div>

    <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" id="editTenantForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Business Information --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-building text-blue-600 mr-3"></i>
                        Business Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Business Name *
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $tenant->name) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="Enter the business name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">
                                Subdomain *
                            </label>
                            <div class="flex">
                                <input type="text" 
                                       id="domain" 
                                       name="domain" 
                                       value="{{ old('domain', $tenant->domain) }}"
                                       required
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('domain') border-red-500 @enderror"
                                       placeholder="company-name">
                                <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    .{{ config('app.domain', 'siteledger.com') }}
                                </span>
                            </div>
                            @error('domain')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-orange-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Changing domain may affect existing users
                            </p>
                        </div>

                        <div>
                            <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Business Type *
                            </label>
                            <select id="business_type" 
                                    name="business_type" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('business_type') border-red-500 @enderror">
                                <option value="">Select business type</option>
                                <option value="construction" {{ old('business_type', $tenant->business_type) === 'construction' ? 'selected' : '' }}>Construction</option>
                                <option value="consulting" {{ old('business_type', $tenant->business_type) === 'consulting' ? 'selected' : '' }}>Consulting</option>
                                <option value="retail" {{ old('business_type', $tenant->business_type) === 'retail' ? 'selected' : '' }}>Retail</option>
                                <option value="manufacturing" {{ old('business_type', $tenant->business_type) === 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="services" {{ old('business_type', $tenant->business_type) === 'services' ? 'selected' : '' }}>Services</option>
                                <option value="other" {{ old('business_type', $tenant->business_type) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('business_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Email *
                            </label>
                            <input type="email" 
                                   id="contact_email" 
                                   name="contact_email" 
                                   value="{{ old('contact_email', $tenant->contact_email) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contact_email') border-red-500 @enderror"
                                   placeholder="business@example.com">
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Phone
                            </label>
                            <input type="tel" 
                                   id="contact_phone" 
                                   name="contact_phone" 
                                   value="{{ old('contact_phone', $tenant->contact_phone) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contact_phone') border-red-500 @enderror"
                                   placeholder="+1 (555) 123-4567">
                            @error('contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Business Address
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                      placeholder="Enter the complete business address">{{ old('address', $tenant->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Subscription & Settings --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-cog text-green-600 mr-3"></i>
                        Subscription & Settings
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="subscription_plan" class="block text-sm font-medium text-gray-700 mb-2">
                                Subscription Plan *
                            </label>
                            <select id="subscription_plan" 
                                    name="subscription_plan" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subscription_plan') border-red-500 @enderror">
                                <option value="basic" {{ old('subscription_plan', $tenant->subscription_plan) === 'basic' ? 'selected' : '' }}>Basic - $29/month</option>
                                <option value="professional" {{ old('subscription_plan', $tenant->subscription_plan) === 'professional' ? 'selected' : '' }}>Professional - $59/month</option>
                                <option value="premium" {{ old('subscription_plan', $tenant->subscription_plan) === 'premium' ? 'selected' : '' }}>Premium - $99/month</option>
                                <option value="enterprise" {{ old('subscription_plan', $tenant->subscription_plan) === 'enterprise' ? 'selected' : '' }}>Enterprise - Custom</option>
                            </select>
                            @error('subscription_plan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_users" class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Users
                            </label>
                            <input type="number" 
                                   id="max_users" 
                                   name="max_users" 
                                   value="{{ old('max_users', $tenant->max_users ?? 5) }}"
                                   min="1"
                                   max="1000"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_users') border-red-500 @enderror"
                                   placeholder="5">
                            @error('max_users')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Current users: {{ $tenant->users_count ?? 0 }}</p>
                        </div>

                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                Timezone
                            </label>
                            <select id="timezone" 
                                    name="timezone" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('timezone') border-red-500 @enderror">
                                <option value="UTC" {{ old('timezone', $tenant->timezone) === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                                <option value="America/New_York" {{ old('timezone', $tenant->timezone) === 'America/New_York' ? 'selected' : '' }}>Eastern Time (GMT-5)</option>
                                <option value="America/Chicago" {{ old('timezone', $tenant->timezone) === 'America/Chicago' ? 'selected' : '' }}>Central Time (GMT-6)</option>
                                <option value="America/Denver" {{ old('timezone', $tenant->timezone) === 'America/Denver' ? 'selected' : '' }}>Mountain Time (GMT-7)</option>
                                <option value="America/Los_Angeles" {{ old('timezone', $tenant->timezone) === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (GMT-8)</option>
                                <option value="Europe/London" {{ old('timezone', $tenant->timezone) === 'Europe/London' ? 'selected' : '' }}>London (GMT+0)</option>
                                <option value="Europe/Paris" {{ old('timezone', $tenant->timezone) === 'Europe/Paris' ? 'selected' : '' }}>Paris (GMT+1)</option>
                                <option value="Asia/Tokyo" {{ old('timezone', $tenant->timezone) === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (GMT+9)</option>
                                <option value="Africa/Kigali" {{ old('timezone', $tenant->timezone ?? 'Africa/Kigali') === 'Africa/Kigali' ? 'selected' : '' }}>Kigali (GMT+2)</option>
                            </select>
                            @error('timezone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Currency
                            </label>
                            <select id="currency" 
                                    name="currency" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('currency') border-red-500 @enderror">
                                <option value="RWF" {{ old('currency', $tenant->currency ?? 'RWF') === 'RWF' ? 'selected' : '' }}>RWF - Rwandan Franc</option>
                                <option value="USD" {{ old('currency', $tenant->currency) === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency', $tenant->currency) === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ old('currency', $tenant->currency) === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="CAD" {{ old('currency', $tenant->currency) === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                            </select>
                            @error('currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Tenant Status
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="inactive" {{ old('status', $tenant->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="trial" {{ old('status', $tenant->status) === 'trial' ? 'selected' : '' }}>Trial</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="trial_ends_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Trial End Date
                            </label>
                            <input type="date" 
                                   id="trial_ends_at" 
                                   name="trial_ends_at" 
                                   value="{{ old('trial_ends_at', $tenant->trial_ends_at ? $tenant->trial_ends_at->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('trial_ends_at') border-red-500 @enderror">
                            @error('trial_ends_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Additional Settings --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-sliders-h text-orange-600 mr-3"></i>
                        Additional Settings
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Business Description
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                      placeholder="Brief description of the business and its services">{{ old('description', $tenant->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Logo
                                </label>
                                @if($tenant->logo)
                                    <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                        <img src="{{ Storage::url($tenant->logo) }}" alt="Current Logo" class="h-16 w-auto object-contain">
                                        <p class="text-xs text-gray-500 mt-1">Current logo</p>
                                    </div>
                                @endif
                                <input type="file" 
                                       id="logo" 
                                       name="logo" 
                                       accept="image/*"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('logo') border-red-500 @enderror">
                                @error('logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, or SVG. Max size 2MB.</p>
                            </div>

                            <div>
                                <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Registration Number
                                </label>
                                <input type="text" 
                                       id="registration_number" 
                                       name="registration_number" 
                                       value="{{ old('registration_number', $tenant->registration_number) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('registration_number') border-red-500 @enderror"
                                       placeholder="Optional business registration number">
                                @error('registration_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Feature Toggles --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Feature Access</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="features[]" 
                                           value="api_access"
                                           {{ in_array('api_access', old('features', $tenant->features ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">API Access</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="features[]" 
                                           value="advanced_reporting"
                                           {{ in_array('advanced_reporting', old('features', $tenant->features ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Advanced Reporting</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="features[]" 
                                           value="white_label"
                                           {{ in_array('white_label', old('features', $tenant->features ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">White Label</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="features[]" 
                                           value="custom_integrations"
                                           {{ in_array('custom_integrations', old('features', $tenant->features ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Custom Integrations</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Danger Zone --}}
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                    <h2 class="text-xl font-bold text-red-800 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                        Danger Zone
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-red-50 rounded-lg">
                            <h3 class="font-semibold text-red-800 mb-2">Reset Tenant Data</h3>
                            <p class="text-sm text-red-600 mb-3">This will delete all data for this tenant. This action cannot be undone.</p>
                            <button type="button" 
                                    onclick="confirmReset()"
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 transition">
                                Reset All Data
                            </button>
                        </div>
                        
                        <div class="p-4 bg-red-50 rounded-lg">
                            <h3 class="font-semibold text-red-800 mb-2">Delete Tenant</h3>
                            <p class="text-sm text-red-600 mb-3">Permanently delete this tenant and all associated data. This action cannot be undone.</p>
                            <button type="button" 
                                    onclick="confirmDelete()"
                                    class="bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-800 transition">
                                Delete Tenant
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Tenant Statistics --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Tenant Statistics</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Users:</span>
                            <span class="font-semibold">{{ $tenant->users_count ?? 0 }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Active Projects:</span>
                            <span class="font-semibold">{{ $tenant->projects_count ?? 0 }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-semibold text-sm">{{ $tenant->created_at->format('M j, Y') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-semibold text-sm">{{ $tenant->updated_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Activity</h3>
                    
                    <div class="space-y-3 text-sm">
                        @if(isset($tenant->recent_activity) && $tenant->recent_activity->count() > 0)
                            @foreach($tenant->recent_activity->take(5) as $activity)
                                <div class="flex items-start space-x-2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-gray-800">{{ $activity->description }}</p>
                                        <p class="text-gray-500 text-xs">{{ $activity->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500 text-center py-4">No recent activity</p>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="space-y-4">
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition">
                            <i class="fas fa-save mr-2"></i>
                            Update Tenant
                        </button>
                        
                        <a href="{{ route('admin.tenants.show', $tenant) }}" 
                           class="w-full bg-gray-200 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-300 transition text-center block">
                            <i class="fas fa-eye mr-2"></i>
                            View Details
                        </a>
                        
                        <a href="{{ route('admin.tenants.index') }}" 
                           class="w-full bg-gray-100 text-gray-600 py-3 px-6 rounded-lg font-semibold hover:bg-gray-200 transition text-center block">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Form validation and enhancements
document.getElementById('editTenantForm').addEventListener('submit', function(e) {
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating Tenant...';
    submitBtn.disabled = true;
});

// Plan selection with user limits
document.getElementById('subscription_plan').addEventListener('change', function() {
    const maxUsersField = document.getElementById('max_users');
    const planLimits = {
        'basic': 5,
        'professional': 20,
        'premium': 100,
        'enterprise': 1000
    };
    
    const selectedPlan = this.value;
    if (planLimits[selectedPlan]) {
        const currentUsers = {{ $tenant->users_count ?? 0 }};
        const newLimit = planLimits[selectedPlan];
        
        if (currentUsers > newLimit) {
            alert(`Warning: This tenant currently has ${currentUsers} users, which exceeds the ${selectedPlan} plan limit of ${newLimit}.`);
        }
        
        maxUsersField.max = newLimit;
        if (parseInt(maxUsersField.value) > newLimit) {
            maxUsersField.value = newLimit;
        }
    }
});

// Real-time domain availability check (only if domain changed)
const originalDomain = '{{ $tenant->domain }}';
document.getElementById('domain').addEventListener('blur', function() {
    const domain = this.value;
    if (domain && domain !== originalDomain) {
        fetch(`/admin/tenants/check-domain?domain=${domain}&exclude={{ $tenant->id }}`)
            .then(response => response.json())
            .then(data => {
                const field = this;
                field.classList.remove('border-red-500', 'border-green-500');
                
                if (data.available) {
                    field.classList.add('border-green-500');
                } else {
                    field.classList.add('border-red-500');
                    alert('This domain is already taken. Please choose another.');
                }
            })
            .catch(error => {
                console.warn('Domain check failed:', error);
            });
    }
});

// Confirmation dialogs for dangerous actions
function confirmReset() {
    if (confirm('Are you sure you want to reset all data for this tenant? This action cannot be undone.')) {
        if (confirm('This will delete ALL tenant data including users, projects, and financial records. Are you absolutely sure?')) {
            // Create a form to submit the reset request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.tenants.reset", $tenant) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    }
}

function confirmDelete() {
    if (confirm('Are you sure you want to permanently delete this tenant? This action cannot be undone.')) {
        if (confirm('This will permanently delete the tenant and ALL associated data. Type "DELETE" to confirm.')) {
            const confirmation = prompt('Type "DELETE" to confirm tenant deletion:');
            if (confirmation === 'DELETE') {
                // Create a form to submit the delete request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.tenants.destroy", $tenant) }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Deletion cancelled. You must type "DELETE" exactly to confirm.');
            }
        }
    }
}

// Auto-save for long forms
let autoSaveTimer;
document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Auto-save draft functionality could be implemented here
            console.log('Auto-saving changes...');
        }, 30000); // Auto-save after 30 seconds of inactivity
    });
});
</script>
@endsection