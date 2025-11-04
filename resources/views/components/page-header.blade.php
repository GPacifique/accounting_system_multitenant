@props(['title', 'subtitle' => null, 'actions' => null])

<div class="page-header">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="page-title">{{ $title }}</h1>
            @if($subtitle)
                <p class="page-subtitle">{{ $subtitle }}</p>
            @endif
        </div>
        @if($actions)
            <div class="flex gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
