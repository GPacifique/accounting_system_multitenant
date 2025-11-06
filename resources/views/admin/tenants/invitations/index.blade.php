{{-- resources/views/admin/tenants/invitations/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tenant Invitations - ' . $tenant->name . ' | SiteLedger')
@section('meta_description', 'Manage user invitations for ' . $tenant->name . ' tenant in SiteLedger multitenant system.')

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
                    <span>Invitations</span>
                </nav>
                <h1 class="text-3xl font-bold flex items-center">
                    <div class="theme-aware-bg-card/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    Tenant Invitations
                </h1>
                <p class="text-purple-100 mt-2">Manage user invitations for {{ $tenant->name }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.tenants.invitations.create', $tenant) }}" 
                   class="theme-aware-bg-card text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition">
                    <i class="fas fa-plus mr-2"></i>
                    Send Invitation
                </a>
                <a href="{{ route('admin.tenants.show', $tenant) }}" 
                   class="bg-purple-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Tenant
                </a>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium theme-aware-text-secondary">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $invitations->where('status', 'pending')->count() }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium theme-aware-text-secondary">Accepted</p>
                    <p class="text-2xl font-bold text-green-600">{{ $invitations->where('status', 'accepted')->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium theme-aware-text-secondary">Expired</p>
                    <p class="text-2xl font-bold text-red-600">{{ $invitations->where('status', 'expired')->count() }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium theme-aware-text-secondary">Total</p>
                    <p class="text-2xl font-bold theme-aware-text-secondary">{{ $invitations->total() }}</p>
                </div>
                <div class="theme-aware-bg-secondary rounded-full p-3">
                    <i class="fas fa-envelope theme-aware-text-secondary"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Invitations Table --}}
    <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b theme-aware-border">
            <h3 class="text-lg font-medium theme-aware-text">All Invitations</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="theme-aware-bg-secondary">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Email & Role
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Invited By
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Expires
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="theme-aware-bg-card divide-y divide-gray-200">
                    @forelse($invitations as $invitation)
                        <tr class="hover:theme-aware-bg-secondary">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium theme-aware-text">{{ $invitation->email }}</div>
                                    <div class="text-sm theme-aware-text-muted">
                                        {{ $invitation->getRoleLabel() }}
                                        @if($invitation->is_admin)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                                Admin
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $invitation->getStatusColorClass() }}">
                                    {{ $invitation->getStatusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm theme-aware-text">{{ $invitation->invitedBy->name }}</div>
                                <div class="text-sm theme-aware-text-muted">{{ $invitation->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm theme-aware-text">{{ $invitation->expires_at->format('M d, Y') }}</div>
                                <div class="text-sm theme-aware-text-muted">{{ $invitation->expires_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($invitation->isPending())
                                        <a href="{{ route('invitations.show', $invitation->token) }}" 
                                           target="_blank"
                                           class="text-blue-600 hover:text-blue-900" 
                                           title="View Invitation">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="resendInvitation({{ $invitation->id }})" 
                                                class="text-green-600 hover:text-green-900" 
                                                title="Resend Invitation">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                        <button onclick="cancelInvitation({{ $invitation->id }})" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Cancel Invitation">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <span class="theme-aware-text-muted">No actions</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center theme-aware-text-muted">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-user-plus text-4xl mb-4 text-gray-300"></i>
                                    <h3 class="text-lg font-medium mb-2">No invitations sent</h3>
                                    <p class="text-sm mb-4">Start by sending an invitation to new team members</p>
                                    <a href="{{ route('admin.tenants.invitations.create', $tenant) }}" 
                                       class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                                        <i class="fas fa-plus mr-2"></i>
                                        Send Invitation
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invitations->hasPages())
            <div class="theme-aware-bg-card px-6 py-4 border-t theme-aware-border">
                {{ $invitations->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function resendInvitation(invitationId) {
    if (confirm('Resend this invitation? The recipient will receive a new email with an updated expiry date.')) {
        // Make API call to resend invitation
        fetch(`/admin/invitations/${invitationId}/resend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to resend invitation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resending the invitation');
        });
    }
}

function cancelInvitation(invitationId) {
    if (confirm('Cancel this invitation? The recipient will no longer be able to accept it.')) {
        // Make API call to cancel invitation
        fetch(`/admin/invitations/${invitationId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to cancel invitation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the invitation');
        });
    }
}
</script>
@endsection