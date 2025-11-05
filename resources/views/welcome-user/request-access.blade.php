{{-- resources/views/welcome-user/request-access.blade.php --}}
@extends('layouts.app')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('title', 'Request Access - SiteLedger')
@section('meta_description', 'Request access to SiteLedger construction finance management platform. Submit your request for system permissions and role assignment.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Request System Access</h1>
                <p class="text-gray-600">Submit a formal request to gain access to SiteLedger's features</p>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-green-800">Request Submitted Successfully!</h3>
                        <p class="text-green-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Request Form --}}
            <div class="bg-white rounded-lg shadow-md p-8">
                <form action="{{ route('welcome.submit-access-request') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    {{-- User Info Display --}}
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Account Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Name:</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Email:</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->email }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Current Role:</span>
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending Activation</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Account Created:</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Requested Role --}}
                    <div>
                        <label for="requested_role" class="block text-sm font-medium text-gray-700 mb-2">
                            Requested Role <span class="text-red-500">*</span>
                        </label>
                        <select name="requested_role" id="requested_role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select a role...</option>
                            <option value="accountant" {{ old('requested_role') == 'accountant' ? 'selected' : '' }}>
                                Accountant - Financial Management & Reporting
                            </option>
                            <option value="manager" {{ old('requested_role') == 'manager' ? 'selected' : '' }}>
                                Manager - Project & Team Management
                            </option>
                        </select>
                        @error('requested_role')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        
                        <div class="mt-2 text-sm text-gray-500">
                            <p class="mb-1"><strong>Accountant:</strong> Access to financial data, payments, expenses, income tracking, and financial reporting.</p>
                            <p><strong>Manager:</strong> Access to project management, employee/worker management, and operational reporting.</p>
                        </div>
                    </div>

                    {{-- Business Justification --}}
                    <div>
                        <label for="business_justification" class="block text-sm font-medium text-gray-700 mb-2">
                            Business Justification <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="business_justification" 
                            id="business_justification" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Briefly explain why you need this access level and how it relates to your job responsibilities..."
                            required
                        >{{ old('business_justification') }}</textarea>
                        @error('business_justification')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Max 500 characters. Be specific about your role and responsibilities.</p>
                    </div>

                    {{-- Additional Message --}}
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Message <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="message" 
                            id="message" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Please provide any additional context about your request, your department, supervisor, or specific features you need access to..."
                            required
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Max 1000 characters. Include any relevant details that would help in reviewing your request.</p>
                    </div>

                    {{-- Agreement --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-2">Terms & Conditions</h4>
                        <div class="text-sm text-blue-800 space-y-2">
                            <p>By submitting this request, you acknowledge that:</p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>You will use system access only for authorized business purposes</li>
                                <li>You will maintain confidentiality of sensitive financial and project data</li>
                                <li>Your access may be monitored and can be revoked at any time</li>
                                <li>You agree to follow all company policies regarding data security</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition">
                            📋 Submit Access Request
                        </button>
                        <a href="{{ route('welcome.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                            ← Back to Welcome
                        </a>
                    </div>
                </form>
            </div>

            {{-- Help Section --}}
            <div class="mt-8 text-center">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Need Help?</h3>
                    <p class="text-gray-600 mb-4">If you have questions about the access request process or need immediate assistance:</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="mailto:admin@siteledger.com" class="text-blue-600 hover:underline">
                            📧 Email Administrator
                        </a>
                        <span class="hidden sm:inline text-gray-300">|</span>
                        <a href="tel:+1234567890" class="text-blue-600 hover:underline">
                            📞 Call Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection