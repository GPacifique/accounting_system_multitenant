{{-- resources/views/components/download-dropdown.blade.php --}}
@props([
    'route',
    'filename' => 'export',
    'label' => 'Download',
    'variant' => 'outline-primary',
    'csvParams' => [],
    'pdfParams' => []
])

<div class="dropdown">
    <button class="btn btn-{{ $variant }} dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-download me-1"></i>{{ $label }}
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route($route . '.csv', array_merge(['filename' => $filename], $csvParams)) }}">
                <i class="fas fa-file-csv text-success me-2"></i>Download CSV
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route($route . '.pdf', array_merge(['filename' => $filename], $pdfParams)) }}">
                <i class="fas fa-file-pdf text-danger me-2"></i>Download PDF
            </a>
        </li>
    </ul>
</div>