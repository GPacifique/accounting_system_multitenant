{{-- Dashboard transactions list card --}}
<div class="theme-aware-bg-card overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
    <div class="px-6 py-5 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
    </div>
    
    <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
        @forelse($items as $item)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            @if($item->project)
                                {{ $item->project->name ?? 'N/A' }}
                            @elseif($item->client)
                                {{ $item->client->name ?? 'N/A' }}
                            @else
                                {{ $item->reference ?? $item->invoice_number ?? 'N/A' }}
                            @endif
                        </p>
                        <p class="text-xs theme-aware-text-muted mt-1">
                            @php
                                $dateValue = $item->{$dateField ?? 'created_at'};
                                $formattedDate = $dateValue instanceof \Carbon\Carbon 
                                    ? $dateValue->format('M d, Y') 
                                    : \Carbon\Carbon::parse($dateValue)->format('M d, Y');
                            @endphp
                            {{ $formattedDate }}
                        </p>
                    </div>
                    <div class="text-right">
                        @if($item->payment_status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($item->payment_status === 'Paid') bg-green-100 text-green-800
                                @elseif($item->payment_status === 'Pending') bg-yellow-100 text-yellow-800
                                @elseif($item->payment_status === 'Overdue') bg-red-100 text-red-800
                                @else bg-gray-100 theme-aware-text
                                @endif">
                                {{ $item->payment_status }}
                            </span>
                        @elseif($item->status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $item->status }}
                            </span>
                        @endif
                        <p class="text-sm font-semibold text-gray-900 mt-2">
                                                <div>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $currencySymbol ?? 'RWF' }} {{ number_format($item->{$amountField ?? 'amount'}, 2) }}
                        </span>
                    </div>
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center">
                <p class="theme-aware-text-muted">{{ $emptyMessage ?? 'No transactions yet' }}</p>
            </div>
        @endforelse
    </div>
</div>
