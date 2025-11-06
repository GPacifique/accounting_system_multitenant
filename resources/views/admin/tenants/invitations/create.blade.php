{{-- resources/views/admin/tenants/invitations/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Send Invitation - ' . $tenant->name . ' | SiteLedger')
@section('meta_description', 'Send user invitation for ' . $tenant->name . ' tenant in SiteLedger.')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <nav class="text-purple-200 text-sm mb-2">
                    <a href="{{ route('admin.tenants.index') }}" class="hover:text-white">Tenants</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.tenants.show', $tenant) }}" class="hover:text-white">{{ $tenant->name }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.tenants.invitations.index', $tenant) }}" class="hover:text-white">Invitations</a>
                    <span class="mx-2">/</span>
                    <span>Send Invitation</span>
                </nav>
                <h1 class="text-3xl font-bold flex items-center">
                    <div class="theme-aware-bg-card/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-paper-plane text-2xl"></i>
                    </div>
                    Send Invitation
                </h1>
                <p class="text-purple-100 mt-2">Invite a new user to join {{ $tenant->name }}</p>
            </div>
            <a href="{{ route('admin.tenants.invitations.index', $tenant) }}" 
               class="bg-purple-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-400 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Invitations
            </a>
        </div>
    </div>

    {{-- Invitation Form --}}
    <div class="theme-aware-bg-card rounded-xl shadow-lg p-8">
        <form method="POST" action="{{ route('admin.tenants.invitations.store', $tenant) }}" id="invitationForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Left Column --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold theme-aware-text border-b pb-2">User Information</h3>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-purple-500 focus:theme-aware-border-focus @error('email') border-red-500 @enderror"
                               placeholder="user@example.com"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select id="role" 
                                name="role" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-purple-500 focus:theme-aware-border-focus @error('role') border-red-500 @enderror"
                                required>
                            <option value="">Select a role</option>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="accountant" {{ old('role') === 'accountant' ? 'selected' : '' }}>Accountant</option>
                            <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        {{-- Role Descriptions --}}
                        <div class="mt-2 text-xs theme-aware-text-muted space-y-1" id="roleDescriptions">
                            <div class="role-desc" data-role="user" style="display: none;">
                                <strong>User:</strong> Basic access to view and create basic records
                            </div>
                            <div class="role-desc" data-role="accountant" style="display: none;">
                                <strong>Accountant:</strong> Full access to financial records and reports
                            </div>
                            <div class="role-desc" data-role="manager" style="display: none;">
                                <strong>Manager:</strong> Access to manage projects, tasks, and team members
                            </div>
                            <div class="role-desc" data-role="admin" style="display: none;">
                                <strong>Administrator:</strong> Full access to all tenant features and settings
                            </div>
                        </div>
                    </div>

                    {{-- Admin Privileges --}}
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_admin" 
                                   name="is_admin" 
                                   value="1"
                                   {{ old('is_admin') ? 'checked' : '' }}
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 theme-aware-border rounded">
                            <label for="is_admin" class="ml-2 block text-sm theme-aware-text">
                                Grant admin privileges
                            </label>
                        </div>
                        <p class="mt-1 text-xs theme-aware-text-muted">
                            Admin privileges allow managing tenant settings and other users
                        </p>
                        @error('is_admin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold theme-aware-text border-b pb-2">Invitation Settings</h3>

                    {{-- Expiry Period --}}
                    <div>
                        <label for="expires_in_days" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Invitation Expires In <span class="text-red-500">*</span>
                        </label>
                        <select id="expires_in_days" 
                                name="expires_in_days" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-purple-500 focus:theme-aware-border-focus @error('expires_in_days') border-red-500 @enderror"
                                required>
                            <option value="1" {{ old('expires_in_days', '7') === '1' ? 'selected' : '' }}>1 Day</option>
                            <option value="3" {{ old('expires_in_days', '7') === '3' ? 'selected' : '' }}>3 Days</option>
                            <option value="7" {{ old('expires_in_days', '7') === '7' ? 'selected' : '' }}>1 Week</option>
                            <option value="14" {{ old('expires_in_days', '7') === '14' ? 'selected' : '' }}>2 Weeks</option>
                            <option value="30" {{ old('expires_in_days', '7') === '30' ? 'selected' : '' }}>1 Month</option>
                        </select>
                        @error('expires_in_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Custom Message --}}
                    <div>
                        <label for="message" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Personal Message (Optional)
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="4"
                                  class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-purple-500 focus:theme-aware-border-focus @error('message') border-red-500 @enderror"
                                  placeholder="Add a personal welcome message for the invitee...">{{ old('message') }}</textarea>
                        <p class="mt-1 text-xs theme-aware-text-muted">This message will be included in the invitation email</p>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Preview Box --}}
                    <div class="theme-aware-bg-secondary rounded-lg p-4">
                        <h4 class="text-sm font-medium theme-aware-text mb-2">Invitation Preview</h4>
                        <div class="text-sm theme-aware-text-secondary space-y-1">
                            <p><strong>Tenant:</strong> {{ $tenant->name }}</p>
                            <p><strong>Business Type:</strong> {{ $tenant->getBusinessTypeLabel() }}</p>
                            <p><strong>From:</strong> {{ Auth::user()->name }}</p>
                            <p id="previewRole"><strong>Role:</strong> <span class="theme-aware-text-muted">Select a role</span></p>
                            <p id="previewAdmin" style="display: none;"><strong>Admin:</strong> <span class="text-purple-600">Yes</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between pt-8 border-t theme-aware-border mt-8">
                <a href="{{ route('admin.tenants.invitations.index', $tenant) }}" 
                   class="px-6 py-3 border theme-aware-border rounded-lg theme-aware-text-secondary hover:theme-aware-bg-secondary transition">
                    Cancel
                </a>
                
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="previewInvitation()"
                            class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-eye mr-2"></i>
                        Preview
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Invitation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const isAdminCheckbox = document.getElementById('is_admin');
    const previewRole = document.getElementById('previewRole');
    const previewAdmin = document.getElementById('previewAdmin');

    // Update role descriptions and preview
    function updateRoleInfo() {
        const selectedRole = roleSelect.value;
        
        // Hide all role descriptions
        document.querySelectorAll('.role-desc').forEach(desc => {
            desc.style.display = 'none';
        });
        
        // Show selected role description
        if (selectedRole) {
            const roleDesc = document.querySelector(`[data-role="${selectedRole}"]`);
            if (roleDesc) {
                roleDesc.style.display = 'block';
            }
            
            // Update preview
            const roleLabels = {
                'user': 'User',
                'accountant': 'Accountant', 
                'manager': 'Manager',
                'admin': 'Administrator'
            };
            previewRole.innerHTML = `<strong>Role:</strong> ${roleLabels[selectedRole] || selectedRole}`;
        } else {
            previewRole.innerHTML = '<strong>Role:</strong> <span class="theme-aware-text-muted">Select a role</span>';
        }
    }

    function updateAdminPreview() {
        if (isAdminCheckbox.checked) {
            previewAdmin.style.display = 'block';
        } else {
            previewAdmin.style.display = 'none';
        }
    }

    roleSelect.addEventListener('change', updateRoleInfo);
    isAdminCheckbox.addEventListener('change', updateAdminPreview);
    
    // Initialize
    updateRoleInfo();
    updateAdminPreview();
});

function previewInvitation() {
    const form = document.getElementById('invitationForm');
    const formData = new FormData(form);
    
    let previewText = `Invitation Preview:\n\n`;
    previewText += `To: ${formData.get('email') || '[Email not specified]'}\n`;
    previewText += `Tenant: {{ $tenant->name }}\n`;
    previewText += `Role: ${formData.get('role') || '[Role not selected]'}\n`;
    previewText += `Admin Privileges: ${formData.get('is_admin') ? 'Yes' : 'No'}\n`;
    previewText += `Expires: ${formData.get('expires_in_days')} days from now\n`;
    
    if (formData.get('message')) {
        previewText += `\nPersonal Message:\n${formData.get('message')}`;
    }
    
    alert(previewText);
}
</script>
@endsection