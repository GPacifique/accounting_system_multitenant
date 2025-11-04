<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SiteLedger') }}</title>
        
        <!-- Favicon and App Icons -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/siteledger-favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo/siteledger-favicon.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo/siteledger-icon.svg') }}">
        
        <!-- Meta Information -->
        <meta name="description" content="SiteLedger - Construction Finance Management System Authentication">
        <meta name="keywords" content="siteledger, login, construction, finance, management">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
@vite(['resources/css/app.css', 'resources/js/app.js']  )
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="flex flex-col items-center">
                <a href="/" class="flex flex-col items-center mb-4">
                    <img src="{{ asset('images/logo/siteledger-logo.svg') }}" alt="SiteLedger Logo" class="h-16 mb-2">
                    <span class="text-2xl font-bold text-blue-800">SITELEDGER</span>
                    <span class="text-sm theme-aware-text-secondary">Construction Finance Management</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 theme-aware-bg-card shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
