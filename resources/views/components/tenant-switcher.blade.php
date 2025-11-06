{{-- resources/views/components/tenant-switcher.blade.php --}}
<div class="relative" x-data="tenantSwitcher">
    {{-- Tenant Switcher Button --}}
    <button @click="isOpen = !isOpen" 
            class="flex items-center space-x-3 px-4 py-3 theme-aware-bg-card theme-aware-border border rounded-lg theme-aware-shadow hover:theme-aware-bg-secondary transition-colors duration-200 w-full max-w-xs">
        
        {{-- Current Tenant Icon --}}
        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
            <span class="text-white font-semibold text-sm">
                {{ Auth::user()->currentTenant() ? strtoupper(substr(Auth::user()->currentTenant()->name, 0, 2)) : 'NT' }}
            </span>
        </div>
        
        {{-- Current Tenant Info --}}
        <div class="flex-grow text-left min-w-0">
            <div class="text-sm font-medium theme-aware-text truncate">
                {{ Auth::user()->currentTenant() ? Auth::user()->currentTenant()->name : 'No Tenant Selected' }}
            </div>
            <div class="text-xs theme-aware-text-muted truncate">
                {{ Auth::user()->currentTenant() ? Auth::user()->currentTenant()->domain : 'Select a tenant' }}
            </div>
        </div>
        
        {{-- Dropdown Arrow --}}
        <div class="flex-shrink-0">
            <svg class="w-5 h-5 theme-aware-text-muted transition-transform duration-200" 
                 :class="{ 'rotate-180': isOpen }" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         @click.away="isOpen = false"
         class="absolute z-50 left-0 mt-2 w-80 theme-aware-bg-card theme-aware-border border rounded-lg theme-aware-shadow py-2">
        
        {{-- Header --}}
        <div class="px-4 py-2 border-b theme-aware-border">
            <h3 class="text-sm font-medium theme-aware-text">Switch Tenant</h3>
            <p class="text-xs theme-aware-text-muted">Select which business you want to work with</p>
        </div>
        
        {{-- Search Input --}}
        <div class="px-4 py-2">
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery"
                       placeholder="Search tenants..."
                       class="w-full pl-8 pr-4 py-2 text-sm border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 theme-aware-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Tenant List --}}
        <div class="max-h-64 overflow-y-auto">
            @if(Auth::user()->tenants()->count() > 0)
                @foreach(Auth::user()->tenants as $tenant)
                    <div x-show="!searchQuery || '{{ strtolower($tenant->name) }}'.includes(searchQuery.toLowerCase())"
                         class="px-4 py-3 hover:theme-aware-bg-secondary cursor-pointer transition-colors duration-150"
                         onclick="switchTenant({{ $tenant->id }})">
                        
                        <div class="flex items-center space-x-3">
                            {{-- Tenant Avatar --}}
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-semibold text-sm">
                                    {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                </span>
                            </div>
                            
                            {{-- Tenant Info --}}
                            <div class="flex-grow min-w-0">
                                <div class="flex items-center space-x-2">
                                    <div class="text-sm font-medium theme-aware-text truncate">
                                        {{ $tenant->name }}
                                    </div>
                                    @if(Auth::user()->currentTenant() && Auth::user()->currentTenant()->id === $tenant->id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Current
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs theme-aware-text-muted truncate">
                                    {{ $tenant->domain }} • {{ ucfirst($tenant->business_type) }}
                                </div>
                                <div class="text-xs theme-aware-text-muted">
                                    Role: {{ Auth::user()->getRoleForTenant($tenant->id) ?? 'User' }}
                                </div>
                            </div>
                            
                            {{-- Status Badge --}}
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($tenant->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="px-4 py-8 text-center">
                    <div class="theme-aware-text-muted mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-14 0h2m-2 0h-2m28 0V9a2 2 0 00-2-2h-2m-2 0h-2m-2 0h-2m-2 0h-2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium theme-aware-text mb-1">No Tenants Available</h3>
                    <p class="text-xs theme-aware-text-muted mb-3">You don't belong to any business tenants yet.</p>
                    @if(!Auth::user()->isSuperAdmin())
                        <button class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                            Request Access
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- Footer Actions --}}
        <div class="border-t border-gray-100 px-4 py-2 mt-2">
            <div class="flex items-center justify-between">
                @if(Auth::user()->isSuperAdmin())
                    <a href="{{ route('admin.tenants.create') }}" 
                       class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Tenant
                    </a>
                @endif
                
                <a href="{{ route('admin.tenants.index') }}" 
                   class="text-xs theme-aware-text-secondary hover:theme-aware-text font-medium">
                    Manage Tenants →
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('tenantSwitcher', () => ({
        isOpen: false,
        searchQuery: '',
        
        init() {
            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.isOpen = false;
                }
            });
        }
    }));
});

function switchTenant(tenantId) {
    // Show loading state
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<div class="flex items-center justify-center"><svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
    
    // Make the switch request
    fetch(`/switch-tenant/${tenantId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ tenant_id: tenantId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
            toast.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Switched to ${data.tenant_name}
                </div>
            `;
            document.body.appendChild(toast);
            
            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Restore button and show error
            button.innerHTML = originalContent;
            alert(data.message || 'Failed to switch tenant');
        }
    })
    .catch(error => {
        // Restore button and show error
        button.innerHTML = originalContent;
        console.error('Error switching tenant:', error);
        alert('An error occurred while switching tenant');
    });
}
</script>