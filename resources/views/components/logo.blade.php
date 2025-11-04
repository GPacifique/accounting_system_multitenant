@props(['size' => 'medium', 'variant' => 'full', 'class' => ''])

@php
    $sizeClasses = [
        'small' => 'h-8',
        'medium' => 'h-12',
        'large' => 'h-16',
        'xl' => 'h-20'
    ];
    
    $logoFiles = [
        'full' => 'siteledger-logo.svg',
        'sidebar' => 'siteledger-sidebar.svg',
        'icon' => 'siteledger-icon.svg'
    ];
    
    $heightClass = $sizeClasses[$size] ?? $sizeClasses['medium'];
    $logoFile = $logoFiles[$variant] ?? $logoFiles['full'];
@endphp

<div class="flex items-center {{ $class }}">
    <img src="{{ asset('images/logo/' . $logoFile) }}" 
         alt="SiteLedger Logo" 
         class="{{ $heightClass }} w-auto">
    
    @if($variant === 'full')
        <div class="ml-3 hidden sm:block">
            <div class="text-lg font-bold text-blue-800">SiteLedger</div>
            <div class="text-xs theme-aware-text-secondary">Construction Finance Management</div>
        </div>
    @endif
</div>