@props([
    'type' => 'primary',
    'href' => null,
    'icon' => null,
    'size' => 'md'
])

@php
$classes = 'btn-enhanced btn-enhanced-' . $type;
$sizeClasses = match($size) {
    'sm' => 'text-xs py-1.5 px-3',
    'lg' => 'text-base py-3 px-6',
    default => 'text-sm py-2 px-4'
};
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes . ' ' . $sizeClasses]) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes . ' ' . $sizeClasses, 'type' => 'button']) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </button>
@endif
