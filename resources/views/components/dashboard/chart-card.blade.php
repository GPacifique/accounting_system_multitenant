{{-- Reusable dashboard chart card component --}}
<div class="theme-aware-bg-card overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
    <div class="px-6 py-5 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        @if($subtitle)
            <p class="theme-aware-text-muted text-sm mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    
    <div class="px-6 py-5" style="min-height: {{ $height ?? '400px' }};">
        <canvas id="{{ $chartId }}" data-chart="{{ json_encode($chartData) }}" data-options="{{ json_encode($chartOptions) }}"></canvas>
    </div>
</div>
