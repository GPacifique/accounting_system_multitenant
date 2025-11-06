{{-- resources/views/admin/tenants/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create New Tenant - System Administration | SiteLedger')
@section('meta_description', 'Create a new business tenant in the SiteLedger multitenant accounting system. Set up business details, subscription plan, and administrator account.')
@section('meta_keywords', 'create tenant, new business setup, tenant creation, multitenant administration, business onboarding')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-green-600 to-teal-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center">
                    <div class="theme-aware-bg-card/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-plus-circle text-2xl"></i>
                    </div>
                    Create New Tenant
                </h1>
                <p class="text-green-100 mt-2">Set up a new business in the accounting system</p>
            </div>
            <a href="{{ route('admin.tenants.index') }}" class="theme-aware-bg-card text-green-600 px-6 py-3 rounded-lg font-semibold hover:bg-green-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Tenants
            </a>
        </div>
    </div>

    <form action="{{ route('admin.tenants.store') }}" method="POST" id="createTenantForm" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Business Information --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                        <i class="fas fa-building text-blue-600 mr-3"></i>
                        Business Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Business Name *
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('name') border-red-500 @enderror"
                                   placeholder="Enter the business name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="domain" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Subdomain *
                            </label>
                            <div class="flex">
                                <input type="text" 
                                       id="domain" 
                                       name="domain" 
                                       value="{{ old('domain') }}"
                                       required
                                       class="flex-1 px-4 py-3 border theme-aware-border rounded-l-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('domain') border-red-500 @enderror"
                                       placeholder="company-name">
                                <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 theme-aware-border theme-aware-bg-secondary theme-aware-text-muted text-sm">
                                    .{{ config('app.domain', 'siteledger.com') }}
                                </span>
                            </div>
                            @error('domain')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs theme-aware-text-muted">This will be the tenant's unique subdomain</p>
                        </div>

                        <div>
                            <label for="business_type" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Business Type *
                            </label>
                            <select id="business_type" 
                                    name="business_type" 
                                    required
                                    class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('business_type') border-red-500 @enderror">
                                <option value="">Select business type</option>
                                <option value="construction" {{ old('business_type') === 'construction' ? 'selected' : '' }}>Construction</option>
                                <option value="consulting" {{ old('business_type') === 'consulting' ? 'selected' : '' }}>Consulting</option>
                                <option value="retail" {{ old('business_type') === 'retail' ? 'selected' : '' }}>Retail</option>
                                <option value="manufacturing" {{ old('business_type') === 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="services" {{ old('business_type') === 'services' ? 'selected' : '' }}>Services</option>
                                <option value="other" {{ old('business_type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('business_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Contact Email *
                            </label>
                            <input type="email" 
                                   id="contact_email" 
                                   name="contact_email" 
                                   value="{{ old('contact_email') }}"
                                   required
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('contact_email') border-red-500 @enderror"
                                   placeholder="business@example.com">
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Contact Phone
                            </label>
                            <input type="tel" 
                                   id="contact_phone" 
                                   name="contact_phone" 
                                   value="{{ old('contact_phone') }}"
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('contact_phone') border-red-500 @enderror"
                                   placeholder="+1 (555) 123-4567">
                            @error('contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Business Address
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3"
                                      class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('address') border-red-500 @enderror"
                                      placeholder="Enter the complete business address">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Subscription & Settings --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                        <i class="fas fa-cog text-green-600 mr-3"></i>
                        Subscription & Settings
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="subscription_plan" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Subscription Plan *
                            </label>
                            <select id="subscription_plan" 
                                    name="subscription_plan" 
                                    required
                                    class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('subscription_plan') border-red-500 @enderror">
                                <option value="basic" {{ old('subscription_plan') === 'basic' ? 'selected' : '' }}>Basic - $29/month</option>
                                <option value="professional" {{ old('subscription_plan') === 'professional' ? 'selected' : '' }}>Professional - $59/month</option>
                                <option value="premium" {{ old('subscription_plan') === 'premium' ? 'selected' : '' }}>Premium - $99/month</option>
                                <option value="enterprise" {{ old('subscription_plan') === 'enterprise' ? 'selected' : '' }}>Enterprise - Custom</option>
                            </select>
                            @error('subscription_plan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_users" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Maximum Users
                            </label>
                            <input type="number" 
                                   id="max_users" 
                                   name="max_users" 
                                   value="{{ old('max_users', 5) }}"
                                   min="1"
                                   max="1000"
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('max_users') border-red-500 @enderror"
                                   placeholder="5">
                            @error('max_users')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="timezone" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Timezone
                            </label>
                            <select id="timezone" 
                                    name="timezone" 
                                    class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('timezone') border-red-500 @enderror">
                                <option value="UTC" {{ old('timezone') === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                                <option value="America/New_York" {{ old('timezone') === 'America/New_York' ? 'selected' : '' }}>Eastern Time (GMT-5)</option>
                                <option value="America/Chicago" {{ old('timezone') === 'America/Chicago' ? 'selected' : '' }}>Central Time (GMT-6)</option>
                                <option value="America/Denver" {{ old('timezone') === 'America/Denver' ? 'selected' : '' }}>Mountain Time (GMT-7)</option>
                                <option value="America/Los_Angeles" {{ old('timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (GMT-8)</option>
                                <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>London (GMT+0)</option>
                                <option value="Europe/Paris" {{ old('timezone') === 'Europe/Paris' ? 'selected' : '' }}>Paris (GMT+1)</option>
                                <option value="Asia/Tokyo" {{ old('timezone') === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (GMT+9)</option>
                                <option value="Africa/Kigali" {{ old('timezone', 'Africa/Kigali') === 'Africa/Kigali' ? 'selected' : '' }}>Kigali (GMT+2)</option>
                            </select>
                            @error('timezone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Default Currency
                            </label>
                            <select id="currency" 
                                    name="currency" 
                                    class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('currency') border-red-500 @enderror">
                                <option value="RWF" {{ old('currency', 'RWF') === 'RWF' ? 'selected' : '' }}>RWF - Rwandan Franc</option>
                                <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="CAD" {{ old('currency') === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                            </select>
                            @error('currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="auto_activate" 
                                       value="1"
                                       {{ old('auto_activate', true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Automatically activate tenant after creation</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Administrator Account --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                        <i class="fas fa-user-shield text-purple-600 mr-3"></i>
                        Administrator Account
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="admin_name" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Administrator Name *
                            </label>
                            <input type="text" 
                                   id="admin_name" 
                                   name="admin_name" 
                                   value="{{ old('admin_name') }}"
                                   required
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('admin_name') border-red-500 @enderror"
                                   placeholder="John Doe">
                            @error('admin_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_email" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Administrator Email *
                            </label>
                            <input type="email" 
                                   id="admin_email" 
                                   name="admin_email" 
                                   value="{{ old('admin_email') }}"
                                   required
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('admin_email') border-red-500 @enderror"
                                   placeholder="admin@company.com">
                            @error('admin_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_password" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Administrator Password *
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="admin_password" 
                                       name="admin_password" 
                                       required
                                       class="w-full px-4 py-3 pr-12 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('admin_password') border-red-500 @enderror"
                                       placeholder="Secure password">
                                <button type="button" 
                                        onclick="togglePassword('admin_password')"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center">
                                    <i class="fas fa-eye theme-aware-text-muted"></i>
                                </button>
                            </div>
                            @error('admin_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_password_confirmation" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Confirm Password *
                            </label>
                            <input type="password" 
                                   id="admin_password_confirmation" 
                                   name="admin_password_confirmation" 
                                   required
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus"
                                   placeholder="Confirm password">
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="send_welcome_email" 
                                       value="1"
                                       {{ old('send_welcome_email', true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Send welcome email to administrator</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Additional Settings --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                        <i class="fas fa-sliders-h text-orange-600 mr-3"></i>
                        Additional Settings
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="description" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                Business Description
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4"
                                      class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('description') border-red-500 @enderror"
                                      placeholder="Brief description of the business and its services">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="logo" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                    Business Logo
                                </label>
                                <input type="file" 
                                       id="logo" 
                                       name="logo" 
                                       accept="image/*"
                                       class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('logo') border-red-500 @enderror">
                                @error('logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs theme-aware-text-muted">PNG, JPG, or SVG. Max size 2MB.</p>
                            </div>

                            <div>
                                <label for="registration_number" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                                    Business Registration Number
                                </label>
                                <input type="text" 
                                       id="registration_number" 
                                       name="registration_number" 
                                       value="{{ old('registration_number') }}"
                                       class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus @error('registration_number') border-red-500 @enderror"
                                       placeholder="Optional business registration number">
                                @error('registration_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Plan Comparison --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold theme-aware-text mb-4">Subscription Plans</h3>
                    
                    <div class="space-y-4">
                        <div class="p-4 border theme-aware-border rounded-lg">
                            <div class="font-semibold theme-aware-text">Basic - $29/month</div>
                            <div class="text-sm theme-aware-text-secondary mt-1">
                                • Up to 5 users<br>
                                • Basic reporting<br>
                                • Email support
                            </div>
                        </div>
                        
                        <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                            <div class="font-semibold text-blue-800">Professional - $59/month</div>
                            <div class="text-sm text-blue-600 mt-1">
                                • Up to 20 users<br>
                                • Advanced reporting<br>
                                • Priority support<br>
                                • API access
                            </div>
                        </div>
                        
                        <div class="p-4 border border-purple-200 rounded-lg">
                            <div class="font-semibold text-purple-800">Premium - $99/month</div>
                            <div class="text-sm text-purple-600 mt-1">
                                • Up to 100 users<br>
                                • Custom reports<br>
                                • Phone support<br>
                                • White-label option
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Creation Preview --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold theme-aware-text mb-4">Creation Preview</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="theme-aware-text-secondary">Tenant will be created with:</span>
                        </div>
                        <div class="pl-3 space-y-1">
                            <div>✓ Isolated database context</div>
                            <div>✓ Default admin user</div>
                            <div>✓ Basic permission structure</div>
                            <div>✓ Sample data (optional)</div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <div class="space-y-4">
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-700 hover:to-teal-700 transition">
                            <i class="fas fa-plus mr-2"></i>
                            Create Tenant
                        </button>
                        
                        <button type="button" 
                                onclick="saveDraft()"
                                class="w-full theme-aware-bg-tertiary theme-aware-text-secondary py-3 px-6 rounded-lg font-semibold hover:bg-gray-300 transition">
                            <i class="fas fa-save mr-2"></i>
                            Save as Draft
                        </button>
                        
                        <a href="{{ route('admin.tenants.index') }}" 
                           class="w-full bg-red-100 text-red-700 py-3 px-6 rounded-lg font-semibold hover:bg-red-200 transition text-center block">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Form validation and enhancements
document.getElementById('createTenantForm').addEventListener('submit', function(e) {
    const password = document.getElementById('admin_password').value;
    const confirmPassword = document.getElementById('admin_password_confirmation').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Tenant...';
    submitBtn.disabled = true;
});

// Auto-generate domain from business name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value.toLowerCase()
        .replace(/[^a-z0-9]/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    
    if (name && !document.getElementById('domain').value) {
        document.getElementById('domain').value = name;
    }
});

// Password visibility toggle
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash theme-aware-text-muted';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye theme-aware-text-muted';
    }
}

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
        maxUsersField.value = planLimits[selectedPlan];
        maxUsersField.max = planLimits[selectedPlan];
    }
});

// Save draft functionality
function saveDraft() {
    const formData = new FormData(document.getElementById('createTenantForm'));
    formData.append('save_as_draft', '1');
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Draft saved successfully!');
        } else {
            alert('Failed to save draft. Please check the form and try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the draft.');
    });
}

// Real-time domain availability check
document.getElementById('domain').addEventListener('blur', function() {
    const domain = this.value;
    if (domain) {
        fetch(`/admin/tenants/check-domain?domain=${domain}`)
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
</script>
@endsection