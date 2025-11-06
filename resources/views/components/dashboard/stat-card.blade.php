{{-- Reusable dashboard statistic card component --}}
@props([
    'title' => 'Statistic',
    'value' => '0',
    'subtitle' => null,
    'icon' => null,
    'iconColor' => 'text-blue-500',
    'borderColor' => 'border-blue-500',
    'trend' => null,
    'trendLabel' => 'last period'
])

<div class="theme-aware-bg-card overflow-hidden shadow-lg rounded-lg border-l-4 {{ $borderColor }} hover:shadow-xl transition-shadow duration-300">
    <div class="px-6 py-5">
        {{-- Header with icon and title --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="theme-aware-text-muted text-sm font-medium uppercase tracking-wide">{{ $title }}</p>
                <p class="text-3xl font-bold theme-aware-text mt-2">{{ $value }}</p>
                @if($subtitle)
                    <p class="theme-aware-text-secondary text-xs mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            @if($icon)
                <div class="text-4xl {{ $iconColor }}">
                    {!! $icon !!}
                </div>
            @endif
        </div>

        {{-- Footer with trend indicator --}}
        @if($trend !== null)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center">
                    @if($trend > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd"></path>
                            </svg>
                            {{ abs($trend) }}%
                        </span>
                    @elseif($trend < 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 13a1 1 0 110 2H7a1 1 0 01-1-1V9a1 1 0 112 0v3.586l4.293-4.293a1 1 0 011.414 1.414L8.414 13H12z" clip-rule="evenodd"></path>
                            </svg>
                            {{ abs($trend) }}%
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium theme-aware-bg-secondary theme-aware-text">
                            No change
                        </span>
                    @endif
                    <span class="theme-aware-text-muted text-xs ml-2">vs. {{ $trendLabel }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
