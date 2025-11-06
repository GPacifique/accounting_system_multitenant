{{-- resources/views/admin/tenants/settings.blade.php --}}
@extends('layouts.app')

@section('title', $tenant->name . ' Settings | SiteLedger')
@section('meta_description', 'Configure tenant-specific settings for ' . $tenant->name . '. Manage business preferences, features, billing, and customization options.')
@section('meta_keywords', 'tenant settings, business configuration, feature management, billing settings, customization')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                {{-- Tenant Avatar --}}
                <div class="w-16 h-16 theme-aware-bg-card/20 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-xl">
                        {{ strtoupper(substr($tenant->name, 0, 2)) }}
                    </span>
                </div>
                
                {{-- Tenant Info --}}
                <div>
                    <h1 class="text-3xl font-bold">{{ $tenant->name }}</h1>
                    <p class="text-indigo-100 mt-1">Tenant Settings & Configuration</p>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-indigo-200">
                        <span>{{ $tenant->domain }}</span>
                        <span>•</span>
                        <span>{{ $tenant->getBusinessTypeLabel() }}</span>
                        <span>•</span>
                        <span class="px-2 py-1 theme-aware-bg-card/20 rounded">{{ $tenant->getSubscriptionPlanLabel() }}</span>
                    </div>
                </div>
            </div>
            
            {{-- Quick Actions --}}
            <div class="flex space-x-3">
                <a href="{{ route('admin.tenants.show', $tenant) }}" class="theme-aware-bg-card/20 text-white px-4 py-2 rounded-lg hover:theme-aware-bg-card/30 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Details
                </a>
                <button onclick="backupTenant()" class="theme-aware-bg-card text-indigo-600 px-4 py-2 rounded-lg font-semibold hover:bg-indigo-50 transition">
                    <i class="fas fa-download mr-2"></i>
                    Backup Data
                </button>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.tenants.update-settings', $tenant) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Settings --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Basic Information --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold theme-aware-text mb-6 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                        Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Business Name</label>
                            <input type="text" name="name" value="{{ $tenant->name }}" 
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Domain</label>
                            <input type="text" name="domain" value="{{ $tenant->domain }}" 
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Business Type</label>
                            <select name="business_type" class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                                @foreach(\App\Models\Tenant::BUSINESS_TYPES as $value => $label)
                                    <option value="{{ $value }}" {{ $tenant->business_type === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Primary Email</label>
                            <input type="email" name="email" value="{{ $tenant->email }}" 
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Phone Number</label>
                            <input type="text" name="phone" value="{{ $tenant->phone }}" 
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                                <option value="active" {{ $tenant->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ $tenant->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="inactive" {{ $tenant->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Business Address</label>
                        <textarea name="address" rows="3" 
                                  class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">{{ $tenant->address }}</textarea>
                    </div>
                </div>

                {{-- Regional Settings --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold theme-aware-text mb-6 flex items-center">
                        <i class="fas fa-globe text-green-600 mr-3"></i>
                        Regional & Localization Settings
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Currency</label>
                            <select name="settings[currency]" class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                                <option value="RWF" {{ ($tenant->getSetting('currency') ?? 'RWF') === 'RWF' ? 'selected' : '' }}>Rwandan Franc (RWF)</option>
                                <option value="USD" {{ ($tenant->getSetting('currency') ?? 'RWF') === 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                <option value="EUR" {{ ($tenant->getSetting('currency') ?? 'RWF') === 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                <option value="KES" {{ ($tenant->getSetting('currency') ?? 'RWF') === 'KES' ? 'selected' : '' }}>Kenyan Shilling (KES)</option>
                                <option value="UGX" {{ ($tenant->getSetting('currency') ?? 'RWF') === 'UGX' ? 'selected' : '' }}>Ugandan Shilling (UGX)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Timezone</label>
                            <select name="settings[timezone]" class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                                <option value="Africa/Kigali" {{ ($tenant->getSetting('timezone') ?? 'Africa/Kigali') === 'Africa/Kigali' ? 'selected' : '' }}>Africa/Kigali</option>
                                <option value="Africa/Nairobi" {{ ($tenant->getSetting('timezone') ?? 'Africa/Kigali') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi</option>
                                <option value="Africa/Kampala" {{ ($tenant->getSetting('timezone') ?? 'Africa/Kigali') === 'Africa/Kampala' ? 'selected' : '' }}>Africa/Kampala</option>
                                <option value="UTC" {{ ($tenant->getSetting('timezone') ?? 'Africa/Kigali') === 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Language</label>
                            <select name="settings[language]" class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                                <option value="en" {{ ($tenant->getSetting('language') ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="rw" {{ ($tenant->getSetting('language') ?? 'en') === 'rw' ? 'selected' : '' }}>Kinyarwanda</option>
                                <option value="fr" {{ ($tenant->getSetting('language') ?? 'en') === 'fr' ? 'selected' : '' }}>French</option>
                                <option value="sw" {{ ($tenant->getSetting('language') ?? 'en') === 'sw' ? 'selected' : '' }}>Swahili</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Date Format</label>
                            <select name="settings[date_format]" class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                                <option value="Y-m-d" {{ ($tenant->getSetting('date_format') ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                <option value="d/m/Y" {{ ($tenant->getSetting('date_format') ?? 'Y-m-d') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="m/d/Y" {{ ($tenant->getSetting('date_format') ?? 'Y-m-d') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="d-M-Y" {{ ($tenant->getSetting('date_format') ?? 'Y-m-d') === 'd-M-Y' ? 'selected' : '' }}>DD-MMM-YYYY</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Financial Year Start</label>
                            <select name="settings[financial_year_start]" class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                                <option value="01-01" {{ ($tenant->getSetting('financial_year_start') ?? '01-01') === '01-01' ? 'selected' : '' }}>January 1st</option>
                                <option value="01-07" {{ ($tenant->getSetting('financial_year_start') ?? '01-01') === '01-07' ? 'selected' : '' }}>July 1st</option>
                                <option value="01-04" {{ ($tenant->getSetting('financial_year_start') ?? '01-01') === '01-04' ? 'selected' : '' }}>April 1st</option>
                                <option value="01-10" {{ ($tenant->getSetting('financial_year_start') ?? '01-01') === '01-10' ? 'selected' : '' }}>October 1st</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Feature Management --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold theme-aware-text mb-6 flex items-center">
                        <i class="fas fa-puzzle-piece text-purple-600 mr-3"></i>
                        Feature Management
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php
                            $features = [
                                'projects' => ['label' => 'Project Management', 'icon' => 'project-diagram'],
                                'tasks' => ['label' => 'Task Management', 'icon' => 'tasks'],
                                'finance' => ['label' => 'Financial Tracking', 'icon' => 'dollar-sign'],
                                'reports' => ['label' => 'Reports & Analytics', 'icon' => 'chart-bar'],
                                'team_management' => ['label' => 'Team Management', 'icon' => 'users'],
                                'advanced_analytics' => ['label' => 'Advanced Analytics', 'icon' => 'chart-line'],
                                'inventory_management' => ['label' => 'Inventory Management', 'icon' => 'boxes'],
                                'client_portal' => ['label' => 'Client Portal', 'icon' => 'user-circle'],
                                'mobile_app' => ['label' => 'Mobile App Access', 'icon' => 'mobile-alt'],
                                'api_access' => ['label' => 'API Access', 'icon' => 'code'],
                                'custom_branding' => ['label' => 'Custom Branding', 'icon' => 'palette'],
                                'backup_restore' => ['label' => 'Backup & Restore', 'icon' => 'shield-alt'],
                            ];
                        @endphp
                        
                        @foreach($features as $key => $feature)
                            <div class="flex items-center justify-between p-4 border theme-aware-border rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 theme-aware-bg-secondary rounded-lg flex items-center justify-center">
                                        <i class="fas fa-{{ $feature['icon'] }} theme-aware-text-secondary"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium theme-aware-text">{{ $feature['label'] }}</h4>
                                        <p class="text-xs theme-aware-text-muted">{{ $key }} module</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="features[{{ $key }}]" value="1" 
                                           {{ $tenant->hasFeature($key) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 theme-aware-bg-tertiary peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:theme-aware-bg-card after:theme-aware-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Security Settings --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold theme-aware-text mb-6 flex items-center">
                        <i class="fas fa-shield-alt text-red-600 mr-3"></i>
                        Security Settings
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Maximum Users</label>
                            <input type="number" name="max_users" value="{{ $tenant->max_users ?? 10 }}" min="1" max="1000"
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Session Timeout (minutes)</label>
                            <input type="number" name="session_timeout" value="{{ $tenant->session_timeout ?? 120 }}" min="5" max="1440"
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        </div>
                        
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between p-4 border theme-aware-border rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium theme-aware-text">Enforce Two-Factor Authentication</h4>
                                    <p class="text-xs theme-aware-text-muted">Require all users to enable 2FA</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enforce_2fa" value="1" 
                                           {{ $tenant->enforce_2fa ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 theme-aware-bg-tertiary peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:theme-aware-bg-card after:theme-aware-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-8">
                {{-- Subscription Information --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold theme-aware-text mb-6 flex items-center">
                        <i class="fas fa-credit-card text-green-600 mr-3"></i>
                        Subscription
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Current Plan</label>
                            <select name="subscription_plan" class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                                @foreach(\App\Models\Tenant::SUBSCRIPTION_PLANS as $value => $label)
                                    <option value="{{ $value }}" {{ $tenant->subscription_plan === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Subscription Expires</label>
                            <input type="date" name="subscription_expires_at" 
                                   value="{{ $tenant->subscription_expires_at ? $tenant->subscription_expires_at->format('Y-m-d') : '' }}"
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium theme-aware-text-secondary mb-2">Trial Ends</label>
                            <input type="date" name="trial_ends_at" 
                                   value="{{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('Y-m-d') : '' }}"
                                   class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        </div>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold theme-aware-text mb-6">Quick Stats</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm theme-aware-text-secondary">Users</span>
                            <span class="text-sm font-semibold theme-aware-text">{{ $tenant->users()->count() }} / {{ $tenant->max_users ?? '∞' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm theme-aware-text-secondary">Projects</span>
                            <span class="text-sm font-semibold theme-aware-text">{{ \App\Models\Project::where('tenant_id', $tenant->id)->count() }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm theme-aware-text-secondary">Tasks</span>
                            <span class="text-sm font-semibold theme-aware-text">{{ \App\Models\Task::where('tenant_id', $tenant->id)->count() }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm theme-aware-text-secondary">Created</span>
                            <span class="text-sm theme-aware-text-muted">{{ $tenant->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm theme-aware-text-secondary">Last Backup</span>
                            <span class="text-sm theme-aware-text-muted">{{ $tenant->last_backup_at ? $tenant->last_backup_at->diffForHumans() : 'Never' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold theme-aware-text mb-6">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <button type="button" onclick="backupTenant()" 
                                class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-download mr-2"></i>
                            Create Backup
                        </button>
                        
                        <button type="button" onclick="resetTenantData()" 
                                class="w-full bg-orange-600 text-white px-4 py-3 rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-refresh mr-2"></i>
                            Reset Data
                        </button>
                        
                        <button type="button" onclick="exportTenantData()" 
                                class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-file-export mr-2"></i>
                            Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="mt-8 flex items-center justify-between">
            <div class="flex space-x-4">
                <a href="{{ route('admin.tenants.show', $tenant) }}" 
                   class="px-6 py-3 border theme-aware-border rounded-lg theme-aware-text-secondary hover:theme-aware-bg-secondary transition">
                    Cancel
                </a>
                <button type="button" onclick="previewChanges()" 
                        class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Preview Changes
                </button>
            </div>
            
            <div class="flex space-x-4">
                <button type="submit" name="action" value="save_draft" 
                        class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-save mr-2"></i>
                    Save Draft
                </button>
                <button type="submit" name="action" value="save_apply" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-check mr-2"></i>
                    Save & Apply
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function backupTenant() {
    if (confirm('Create a backup of all tenant data?')) {
        fetch(`/admin/tenants/{{ $tenant->id }}/backup`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Backup created successfully!');
            } else {
                alert('Backup failed: ' + data.message);
            }
        });
    }
}

function resetTenantData() {
    if (confirm('Are you sure you want to reset all tenant data? This action cannot be undone.')) {
        if (confirm('This will permanently delete all projects, tasks, and financial data. Continue?')) {
            // Implementation would go here
            alert('Data reset functionality would be implemented here.');
        }
    }
}

function exportTenantData() {
    const format = prompt('Export format (csv/excel/json):', 'csv');
    if (format) {
        window.open(`/admin/tenants/{{ $tenant->id }}/export?format=${format}`, '_blank');
    }
}

function previewChanges() {
    // Collect form data and show preview modal
    alert('Preview functionality would show a modal with all changes before saving.');
}
</script>
@endsection