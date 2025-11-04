@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'enhanced-card']) }}>
    @if($title)
        <div class="mb-4">
            <h3 class="text-lg font-semibold theme-aware-text">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-sm theme-aware-text-secondary mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    <div>
        {{ $slot }}
    </div>
</div>
