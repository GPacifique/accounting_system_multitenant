{{-- resources/views/welcome-user.blade.php --}}
@extends('layouts.app')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('title', 'Welcome to SiteLedger - Construction Finance Management System')
@section('meta_description', 'Welcome to SiteLedger - Your comprehensive construction finance management platform. Learn about our features and request access to start managing your construction projects.')
@section('meta_keywords', 'construction finance, project management, welcome, system features, access request')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="text-center mb-12">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Welcome to <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">SiteLedger</span> 🎉
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Hello <strong>{{ $user->name }}</strong>! Your account has been successfully created. 
                SiteLedger is your comprehensive construction finance management platform.
            </p>
        </div>

        {{-- Status Card --}}
        <div class="max-w-4xl mx-auto mb-12">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">🔐 Account Pending Activation</h3>
                        <p class="text-yellow-700 mb-4">
                            Your account has been created but requires administrator approval before you can access the system features. 
                            This security measure ensures proper access control for our construction finance management platform.
                        </p>
                        <div class="bg-yellow-100 rounded-lg p-4">
                            <h4 class="font-medium text-yellow-800 mb-2">What happens next?</h4>
                            <ol class="text-sm text-yellow-700 space-y-1">
                                <li>1. 📧 An administrator has been notified of your registration</li>
                                <li>2. 👤 They will review your account and assign appropriate permissions</li>
                                <li>3. 📨 You'll receive an email when your account is activated</li>
                                <li>4. 🚀 You can then access all relevant system features</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Features --}}
        <div class="max-w-6xl mx-auto mb-12">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">🏗️ What You'll Have Access To</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Project Management --}}
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500 hover:shadow-lg transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Project Management</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Track construction projects from start to finish with comprehensive project oversight and budget management.</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Project timelines & milestones</li>
                        <li>• Budget tracking & forecasting</li>
                        <li>• Progress monitoring</li>
                        <li>• Document management</li>
                    </ul>
                </div>

                {{-- Financial Management --}}
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500 hover:shadow-lg transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Financial Management</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Complete financial oversight with income tracking, expense management, and comprehensive reporting.</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Income & expense tracking</li>
                        <li>• Payment management</li>
                        <li>• Financial reports</li>
                        <li>• Cash flow analysis</li>
                    </ul>
                </div>

                {{-- Team Management --}}
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-purple-500 hover:shadow-lg transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Team Management</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Manage employees, workers, and team members with role-based access control and payroll tracking.</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Employee management</li>
                        <li>• Worker assignments</li>
                        <li>• Payroll tracking</li>
                        <li>• Performance monitoring</li>
                    </ul>
                </div>

                {{-- Reporting & Analytics --}}
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-orange-500 hover:shadow-lg transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Reporting & Analytics</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Generate comprehensive reports and analytics to make data-driven decisions for your construction business.</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Financial reports</li>
                        <li>• Project analytics</li>
                        <li>• Performance metrics</li>
                        <li>• Export capabilities</li>
                    </ul>
                </div>

                {{-- Client Management --}}
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-indigo-500 hover:shadow-lg transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Client Management</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Maintain detailed client relationships with contact management and project association.</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Client database</li>
                        <li>• Contact management</li>
                        <li>• Project associations</li>
                        <li>• Communication history</li>
                    </ul>
                </div>

                {{-- Security & Compliance --}}
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-500 hover:shadow-lg transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Security & Compliance</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Enterprise-grade security with role-based access control and audit trails for compliance.</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Role-based permissions</li>
                        <li>• Secure data handling</li>
                        <li>• Audit trails</li>
                        <li>• Backup systems</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Benefits Section --}}
        <div class="max-w-4xl mx-auto mb-12">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">🚀 Why Choose SiteLedger?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 font-bold text-lg">💰</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Cost Control</h3>
                        <p class="text-gray-600">Track every expense, monitor budgets in real-time, and prevent cost overruns with advanced forecasting.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-lg">⏱️</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Time Savings</h3>
                        <p class="text-gray-600">Automate repetitive tasks, streamline workflows, and reduce administrative overhead by up to 70%.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 font-bold text-lg">📊</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Better Decisions</h3>
                        <p class="text-gray-600">Make data-driven decisions with comprehensive analytics and real-time reporting capabilities.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span class="text-yellow-600 font-bold text-lg">🔒</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Data Security</h3>
                        <p class="text-gray-600">Enterprise-grade security ensures your financial data is protected with bank-level encryption.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Next Steps --}}
        <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg text-white p-8 text-center">
                <h2 class="text-2xl font-bold mb-4">🎯 Ready to Get Started?</h2>
                <p class="text-blue-100 mb-6">
                    While you wait for account activation, here's what you can do:
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-blue-700 bg-opacity-50 rounded-lg p-4">
                        <h3 class="font-semibold mb-2">📖 Read Documentation</h3>
                        <p class="text-sm text-blue-100">Familiarize yourself with system features and best practices.</p>
                    </div>
                    <div class="bg-blue-700 bg-opacity-50 rounded-lg p-4">
                        <h3 class="font-semibold mb-2">📞 Contact Support</h3>
                        <p class="text-sm text-blue-100">Reach out if you have questions about system capabilities.</p>
                    </div>
                    <div class="bg-blue-700 bg-opacity-50 rounded-lg p-4">
                        <h3 class="font-semibold mb-2">⚡ Request Access</h3>
                        <p class="text-sm text-blue-100">Send a formal access request to expedite your activation.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('welcome.request-access') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                        📋 Request Access Now
                    </a>
                    <a href="mailto:admin@siteledger.com" class="bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                        📧 Contact Administrator
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-12 text-gray-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'SiteLedger') }}. All rights reserved.</p>
            <p class="text-sm mt-2">Professional Construction Finance Management Platform</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .shadow-md { box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }
    .hover\:shadow-lg:hover { box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15); }
    .transition { transition: all 0.3s ease; }
</style>
@endpush