@php
    $variant = $attributes->get('variant', 'full');
    $size = $attributes->get('size', 'medium');
    
    $logoFiles = [
        'full' => 'siteledger-logo.svg',
        'sidebar' => 'siteledger-sidebar.svg',
        'icon' => 'siteledger-icon.svg'
    ];
    
    $logoFile = $logoFiles[$variant] ?? $logoFiles['full'];
@endphp

<img src="{{ asset('images/logo/' . $logoFile) }}" 
     alt="SiteLedger Logo" 
     {{ $attributes->merge(['class' => 'w-auto']) }}>
