{{-- Reusable dashboard category breakdown card --}}
<div class="theme-aware-bg-card overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
    <div class="px-6 py-5 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
    </div>
    
    <div class="divide-y divide-gray-200">
        @forelse($items as $item)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $item['category'] ?? 'Unknown' }}</p>
                        <p class="text-xs theme-aware-text-muted mt-1">{{ $item['count'] ?? 0 }} {{ $countLabel ?? 'items' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-{{ $barColor ?? 'blue' }}-500 h-2 rounded-full" 
                                 style="width: {{ $maxValue > 0 ? (($item['total'] ?? 0) / $maxValue) * 100 : 0 }}%"></div>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 mt-2">
                                                    <span class="text-sm font-semibold text-gray-900">
                            {{ $currencySymbol ?? 'RWF' }} {{ number_format($item['total'] ?? 0, 2) }}
                        </span>
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center">
                <p class="theme-aware-text-muted">{{ $emptyMessage ?? 'No data available' }}</p>
            </div>
        @endforelse
    </div>
</div>
