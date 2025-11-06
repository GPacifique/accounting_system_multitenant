{{-- resources/views/invitations/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Invitation to ' . $invitation->tenant->name . ' | SiteLedger')
@section('meta_description', 'Accept your invitation to join ' . $invitation->tenant->name . ' on SiteLedger.')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        {{-- Header --}}
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-xl">
                    {{ strtoupper(substr($invitation->tenant->name, 0, 2)) }}
                </span>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold theme-aware-text">
                You're Invited!
            </h2>
            <p class="mt-2 text-sm theme-aware-text-secondary">
                Join <strong>{{ $invitation->tenant->name }}</strong> on SiteLedger
            </p>
        </div>

        {{-- Invitation Card --}}
        <div class="theme-aware-bg-card rounded-xl shadow-lg p-8">
            {{-- Invitation Details --}}
            <div class="space-y-6">
                {{-- Tenant Info --}}
                <div class="text-center border-b theme-aware-border pb-6">
                    <h3 class="text-xl font-semibold theme-aware-text">{{ $invitation->tenant->name }}</h3>
                    <p class="text-sm theme-aware-text-muted">{{ $invitation->tenant->getBusinessTypeLabel() }}</p>
                    @if($invitation->tenant->domain)
                        <p class="text-xs theme-aware-text-muted">{{ $invitation->tenant->domain }}</p>
                    @endif
                </div>

                {{-- Role & Permissions --}}
                <div class="theme-aware-bg-secondary rounded-lg p-4">
                    <h4 class="text-sm font-medium theme-aware-text mb-2">Your Role & Permissions</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm theme-aware-text-secondary">Role:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $invitation->getRoleLabel() }}
                            </span>
                        </div>
                        @if($invitation->is_admin)
                            <div class="flex items-center justify-between">
                                <span class="text-sm theme-aware-text-secondary">Admin Privileges:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Yes
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Invitation Details --}}
                <div class="space-y-3">
                    <div class="flex items-center text-sm theme-aware-text-secondary">
                        <i class="fas fa-user w-4 mr-3"></i>
                        <span>Invited by <strong>{{ $invitation->invitedBy->name }}</strong></span>
                    </div>
                    <div class="flex items-center text-sm theme-aware-text-secondary">
                        <i class="fas fa-envelope w-4 mr-3"></i>
                        <span>Sent to <strong>{{ $invitation->email }}</strong></span>
                    </div>
                    <div class="flex items-center text-sm theme-aware-text-secondary">
                        <i class="fas fa-clock w-4 mr-3"></i>
                        <span>Expires {{ $invitation->expires_at->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- Personal Message --}}
                @if($invitation->message)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Personal Message</h4>
                        <p class="text-sm text-blue-800">{{ $invitation->message }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                @auth
                    @if(Auth::user()->email === $invitation->email)
                        <div class="flex space-x-3">
                            <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}" class="flex-1">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                                    <i class="fas fa-check mr-2"></i>
                                    Accept Invitation
                                </button>
                            </form>
                            <a href="{{ route('invitations.decline', $invitation->token) }}" 
                               class="flex-1 flex justify-center py-3 px-4 border theme-aware-border rounded-lg shadow-sm text-sm font-medium theme-aware-text-secondary theme-aware-bg-card hover:theme-aware-bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                                <i class="fas fa-times mr-2"></i>
                                Decline
                            </a>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Email Mismatch</h3>
                                    <p class="mt-1 text-sm text-yellow-700">
                                        You're logged in as <strong>{{ Auth::user()->email }}</strong>, but this invitation is for <strong>{{ $invitation->email }}</strong>.
                                        Please log in with the correct email address to accept this invitation.
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ route('logout') }}" 
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                           class="text-sm font-medium text-yellow-800 hover:text-yellow-900">
                                            Log out and try again →
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Login Required</h3>
                                    <p class="mt-1 text-sm text-blue-700">
                                        You need to log in or create an account to accept this invitation.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('login', ['return' => route('invitations.show', $invitation->token)]) }}" 
                               class="flex-1 flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Log In
                            </a>
                            <a href="{{ route('register', ['return' => route('invitations.show', $invitation->token)]) }}" 
                               class="flex-1 flex justify-center py-3 px-4 border theme-aware-border rounded-lg shadow-sm text-sm font-medium theme-aware-text-secondary theme-aware-bg-card hover:theme-aware-bg-secondary transition">
                                <i class="fas fa-user-plus mr-2"></i>
                                Sign Up
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center">
            <p class="text-xs theme-aware-text-muted">
                This invitation will expire on {{ $invitation->expires_at->format('M d, Y \a\t H:i') }}
            </p>
            <p class="text-xs theme-aware-text-muted mt-2">
                Powered by <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800">SiteLedger</a>
            </p>
        </div>
    </div>
</div>
@endsection