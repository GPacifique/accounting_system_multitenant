{{-- resources/views/projects/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create New Project - Construction Project Setup | SiteLedger')
@section('meta_description', 'Create a new construction project in SiteLedger. Set up project details, budget, timeline, client information, and team assignments for comprehensive project management.')
@section('meta_keywords', 'create project, new project setup, construction project creation, project management, project planning, budget setup')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="max-w-4xl mx-auto p-6">
    {{-- Header with gradient background --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center">
                    <span class="theme-aware-bg-card/20 rounded-lg p-2 mr-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    Create New Project
                </h1>
                <p class="text-blue-100 mt-2">Fill in the details below to create a new project and link it to a client</p>
            </div>
            <a href="{{ route('projects.index') }}" 
               class="hidden md:flex items-center gap-2 px-4 py-2 theme-aware-bg-card/10 hover:theme-aware-bg-card/20 backdrop-blur-sm rounded-lg transition border border-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Projects
            </a>
        </div>
    </div>

    {{-- Mobile back button --}}
    <div class="md:hidden mb-4">
        <a href="{{ route('projects.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 theme-aware-bg-card rounded-lg shadow-sm border hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Projects
        </a>
    </div>

    {{-- Validation summary --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm animate-shake">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <strong class="block text-red-800 font-semibold mb-2">⚠️ Please fix the following errors:</strong>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-red-700">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form id="createProjectForm" action="{{ route('projects.store') }}" method="POST" class="theme-aware-bg-card rounded-xl shadow-lg p-8 needs-validation" novalidate>
        @csrf

        {{-- Section: Basic Information --}}
        <div class="mb-8">
            <h2 class="text-xl font-bold theme-aware-text mb-4 flex items-center pb-3 border-b-2 border-blue-500">
                <span class="bg-blue-100 text-blue-600 rounded-lg p-2 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                Basic Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Client select --}}
                <div class="md:col-span-2">
                    <label for="client_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Client <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <select id="client_id" name="client_id" required
                            class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('client_id') border-red-400 ring-2 ring-red-200 @enderror">
                        <option value="">-- Select a client --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs theme-aware-text-muted mt-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Choose the client this project belongs to
                    </p>
                    @error('client_id') <p class="text-sm text-red-600 mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                </div>

                {{-- Project name --}}
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Project Name <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <input id="name" name="name" value="{{ old('name') }}" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('name') border-red-400 ring-2 ring-red-200 @enderror"
                           placeholder="e.g. Kigali Bridge Rehabilitation Project">
                    @error('name') <p class="text-sm text-red-600 mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                </div>

                {{-- Start date --}}
                <div>
                    <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Start Date
                        </span>
                    </label>
                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}"
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('start_date') border-red-400 ring-2 ring-red-200 @enderror">
                    <p class="text-xs theme-aware-text-muted mt-2">Optional - Leave empty if TBD</p>
                    @error('start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- End date --}}
                <div>
                    <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            End Date
                        </span>
                    </label>
                    <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}"
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('end_date') border-red-400 ring-2 ring-red-200 @enderror">
                    <p id="dateHelp" class="text-xs theme-aware-text-muted mt-2">Must be same or after start date</p>
                    @error('end_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div class="md:col-span-2">
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Project Status
                        </span>
                    </label>
                    <select id="status" name="status"
                            class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('status') border-red-400 ring-2 ring-red-200 @enderror">
                        @php $status = old('status', 'planned'); @endphp
                        <option value="planned" {{ $status === 'planned' ? 'selected' : '' }}>📋 Planned</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>✅ Active</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>🎉 Completed</option>
                        <option value="on-hold" {{ $status === 'on-hold' ? 'selected' : '' }}>⏸️ On Hold</option>
                    </select>
                    <p class="text-xs theme-aware-text-muted mt-2">Select the current status of the project</p>
                    @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section: Financial Information --}}
        <div class="mb-8">
            <h2 class="text-xl font-bold theme-aware-text mb-4 flex items-center pb-3 border-b-2 border-green-500">
                <span class="bg-green-100 text-green-600 rounded-lg p-2 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                Financial Information
            </h2>

            <div class="grid grid-cols-1 gap-6">
                {{-- Contract value --}}
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-5 border-2 border-emerald-200">
                    <label for="contract_value" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Contract Value (Total)
                        </span>
                    </label>
                    <div class="relative">
                        <input id="contract_value" name="contract_value" type="number" step="0.01" min="0"
                               value="{{ old('contract_value', '') }}"
                               class="w-full border-2 border-emerald-300 rounded-lg px-4 py-3 pr-32 text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition theme-aware-bg-card @error('contract_value') border-red-400 ring-2 ring-red-200 @enderror"
                               placeholder="0.00" aria-describedby="contractHelp">
                        <select id="contract_currency" name="contract_currency" class="absolute right-2 top-1/2 -translate-y-1/2 border-2 border-emerald-300 rounded-lg px-3 py-2 text-sm font-semibold theme-aware-bg-card focus:ring-2 focus:ring-emerald-500">
                            @php $cur = old('contract_currency', 'RWF'); @endphp
                            <option value="RWF" {{ $cur === 'RWF' ? 'selected' : '' }}>🇷🇼 RWF</option>
                            <option value="USD" {{ $cur === 'USD' ? 'selected' : '' }}>💵 USD</option>
                            <option value="EUR" {{ $cur === 'EUR' ? 'selected' : '' }}>💶 EUR</option>
                        </select>
                    </div>
                    <p id="contractHelp" class="text-xs theme-aware-text-secondary mt-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Enter the total contract amount
                    </p>
                    @error('contract_value') <p class="text-sm text-red-600 mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Amount paid --}}
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-5 border-2 border-green-200">
                        <label for="amount_paid" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Amount Paid
                            </span>
                        </label>
                        <div class="relative">
                            <input id="amount_paid" name="amount_paid" type="number" step="0.01" min="0"
                                   value="{{ old('amount_paid', '') }}"
                                   class="w-full border-2 border-green-300 rounded-lg px-4 py-3 pr-32 font-semibold focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition theme-aware-bg-card @error('amount_paid') border-red-400 ring-2 ring-red-200 @enderror"
                                   placeholder="0.00">
                            <select id="amount_paid_currency" name="amount_paid_currency" class="absolute right-2 top-1/2 -translate-y-1/2 border-2 border-green-300 rounded-lg px-3 py-2 text-sm font-semibold theme-aware-bg-card">
                                @php $paidCur = old('amount_paid_currency', $cur ?? 'RWF'); @endphp
                                <option value="RWF" {{ $paidCur === 'RWF' ? 'selected' : '' }}>🇷🇼 RWF</option>
                                <option value="USD" {{ $paidCur === 'USD' ? 'selected' : '' }}>💵 USD</option>
                                <option value="EUR" {{ $paidCur === 'EUR' ? 'selected' : '' }}>💶 EUR</option>
                            </select>
                        </div>
                        <p class="text-xs theme-aware-text-secondary mt-2">Amount already paid</p>
                        @error('amount_paid') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Amount remaining --}}
                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg p-5 border-2 border-orange-200">
                        <label for="amount_remaining" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Amount Remaining
                            </span>
                        </label>
                        <div class="relative">
                            <input id="amount_remaining" name="amount_remaining" type="number" step="0.01" min="0"
                                   value="{{ old('amount_remaining', '') }}"
                                   class="w-full border-2 border-orange-300 rounded-lg px-4 py-3 pr-32 font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition theme-aware-bg-card @error('amount_remaining') border-red-400 ring-2 ring-red-200 @enderror"
                                   placeholder="0.00">
                            <select id="amount_remaining_currency" name="amount_remaining_currency" class="absolute right-2 top-1/2 -translate-y-1/2 border-2 border-orange-300 rounded-lg px-3 py-2 text-sm font-semibold theme-aware-bg-card">
                                @php $remCur = old('amount_remaining_currency', $cur ?? 'RWF'); @endphp
                                <option value="RWF" {{ $remCur === 'RWF' ? 'selected' : '' }}>🇷🇼 RWF</option>
                                <option value="USD" {{ $remCur === 'USD' ? 'selected' : '' }}>💵 USD</option>
                                <option value="EUR" {{ $remCur === 'EUR' ? 'selected' : '' }}>💶 EUR</option>
                            </select>
                        </div>
                        <p class="text-xs theme-aware-text-secondary mt-2">Amount still to be paid</p>
                        @error('amount_remaining') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Additional Details --}}
        <div class="mb-8">
            <h2 class="text-xl font-bold theme-aware-text mb-4 flex items-center pb-3 border-b-2 border-purple-500">
                <span class="bg-purple-100 text-purple-600 rounded-lg p-2 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                Additional Details
            </h2>

            {{-- Description / notes --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        Description / Notes
                    </span>
                </label>
                <textarea id="description" name="description" rows="5"
                          class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('description') border-red-400 ring-2 ring-red-200 @enderror"
                          placeholder="Enter project description, scope, special terms, or any additional notes...">{{ old('description') }}</textarea>
                <p class="text-xs theme-aware-text-muted mt-2">Optional - Add any relevant details about the project</p>
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 pt-6 border-t-2 border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm theme-aware-text-secondary">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Fields marked with <span class="text-red-500 font-semibold">*</span> are required
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('projects.index') }}" 
                       class="flex-1 sm:flex-none px-6 py-3 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition text-center">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex-1 sm:flex-none px-8 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition transform hover:scale-105 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Project
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    /* Enhanced styling for polished look */
    .shadow-lg { box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); }
    .shadow-xl { box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); }
    
    /* Smooth transitions */
    input, select, textarea, button, a {
        transition: all 0.3s ease;
    }
    
    /* Focus states */
    input:focus, select:focus, textarea:focus {
        transform: translateY(-1px);
    }
    
    /* Shake animation for errors */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
    
    /* Hover effects */
    select option {
        padding: 8px;
    }
    
    /* Currency dropdown improvements */
    select[id*="currency"] {
        min-width: 85px;
        max-width: 110px;
        z-index: 10;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Ensure input text doesn't get covered by dropdown */
    input[type="number"] {
        padding-right: 8rem !important;
    }
    
    /* Mobile responsive adjustments for currency dropdowns */
    @media (max-width: 640px) {
        select[id*="currency"] {
            position: static !important;
            margin-top: 0.5rem;
            width: 100%;
        }
        
        input[type="number"] {
            padding-right: 1rem !important;
        }
        
        .relative {
            display: block;
        }
    }
    
    /* Custom scrollbar for textarea */
    textarea::-webkit-scrollbar {
        width: 8px;
    }
    
    textarea::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    textarea::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    textarea::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Responsive adjustments */
    @media (max-width: 640px) {
        .max-w-4xl {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createProjectForm');
    
    if (form) {
        // Form validation with enhanced UX
        form.addEventListener('submit', function (e) {
            // Date validation
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            
            if (start && end) {
                const startDate = new Date(start);
                const endDate = new Date(end);
                
                if (endDate < startDate) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Show error with animation
                    const endInput = document.getElementById('end_date');
                    endInput.classList.add('border-red-400', 'ring-2', 'ring-red-200');
                    
                    alert('⚠️ End date cannot be before start date.');
                    endInput.focus();
                    
                    setTimeout(() => {
                        endInput.classList.remove('border-red-400', 'ring-2', 'ring-red-200');
                    }, 3000);
                    
                    return false;
                }
            }

            // HTML5 validation
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            
            form.classList.add('was-validated');
        }, false);
    }

    // Auto-format contract value
    const contractInput = document.getElementById('contract_value');
    if (contractInput) {
        contractInput.addEventListener('blur', function () {
            if (this.value === '') return;
            const num = parseFloat(this.value);
            if (!isNaN(num)) {
                this.value = num.toFixed(2);
            }
        });
        
        contractInput.addEventListener('input', function () {
            if (this.value && parseFloat(this.value) < 0) {
                this.value = 0;
            }
        });
    }

    // Auto-format amount paid
    const amountPaidInput = document.getElementById('amount_paid');
    if (amountPaidInput) {
        amountPaidInput.addEventListener('blur', function () {
            if (this.value === '') return;
            const num = parseFloat(this.value);
            if (!isNaN(num)) {
                this.value = num.toFixed(2);
            }
        });
    }

    // Auto-format amount remaining
    const amountRemainingInput = document.getElementById('amount_remaining');
    if (amountRemainingInput) {
        amountRemainingInput.addEventListener('blur', function () {
            if (this.value === '') return;
            const num = parseFloat(this.value);
            if (!isNaN(num)) {
                this.value = num.toFixed(2);
            }
        });
    }

    // Auto-select first client if only one exists
    const clientSelect = document.getElementById('client_id');
    if (clientSelect && clientSelect.options.length === 2 && !clientSelect.value) {
        clientSelect.selectedIndex = 1;
        clientSelect.dispatchEvent(new Event('change'));
    }

    // Add loading state to submit button
    const submitBtn = form?.querySelector('button[type="submit"]');
    if (submitBtn) {
        form.addEventListener('submit', function(e) {
            if (form.checkValidity()) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin w-5 h-5 mr-2 inline" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating...
                `;
            }
        });
    }
});
</script>
@endpush

