@props(['type' => 'info', 'dismissible' => false])

@php
$classes = 'alert-enhanced alert-enhanced-' . $type;
$icon = match($type) {
    'success' => 'fas fa-check-circle',
    'warning' => 'fas fa-exclamation-triangle',
    'danger' => 'fas fa-times-circle',
    default => 'fas fa-info-circle'
};
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <i class="{{ $icon }}"></i>
    <div class="flex-1">
        {{ $slot }}
    </div>
    @if($dismissible)
        <button type="button" class="text-current opacity-70 hover:opacity-100 transition-opacity" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    @endif
</div>
