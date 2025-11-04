{{-- resources/views/components/download-buttons.blade.php --}}
@props([
    'route',
    'filename' => 'export',
    'size' => 'sm',
    'csvParams' => [],
    'pdfParams' => []
])

@php
    $buttonClass = match($size) {
        'xs' => 'btn btn-sm px-2 py-1',
        'sm' => 'btn btn-sm',
        'md' => 'btn',
        'lg' => 'btn btn-lg',
        default => 'btn btn-sm'
    };
    
    $iconSize = match($size) {
        'xs' => 'fa-xs',
        'sm' => 'fa-sm',
        'md' => '',
        'lg' => 'fa-lg',
        default => 'fa-sm'
    };
@endphp

<div class="btn-group" role="group" aria-label="Download options">
    {{-- CSV Download --}}
    <a href="{{ route($route . '.csv', array_merge(['filename' => $filename], $csvParams)) }}" 
       class="{{ $buttonClass }} btn-outline-success" 
       title="Download as CSV"
       data-bs-toggle="tooltip">
        <i class="fas fa-file-csv {{ $iconSize }} me-1"></i>
        @if($size !== 'xs')<span>CSV</span>@endif
    </a>
    
    {{-- PDF Download --}}
    <a href="{{ route($route . '.pdf', array_merge(['filename' => $filename], $pdfParams)) }}" 
       class="{{ $buttonClass }} btn-outline-danger" 
       title="Download as PDF"
       data-bs-toggle="tooltip">
        <i class="fas fa-file-pdf {{ $iconSize }} me-1"></i>
        @if($size !== 'xs')<span>PDF</span>@endif
    </a>
</div>

{{-- Initialize tooltips --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush