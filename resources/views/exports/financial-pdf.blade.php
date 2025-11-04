{{-- resources/views/exports/financial-pdf.blade.php --}}
@extends('exports.pdf-template')

@section('content')
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Type</th>
            <th>Description</th>
            <th>Amount (RWF)</th>
            <th>Status</th>
            @if(isset($showProject) && $showProject)
            <th>Project</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</td>
            <td>{{ $item->type ?? ucfirst(class_basename($item)) }}</td>
            <td>{{ $item->description ?? $item->title ?? $item->reference ?? 'N/A' }}</td>
            <td class="text-right amount">RWF {{ number_format($item->amount ?? $item->amount_received ?? 0, 0) }}</td>
            <td class="text-center">
                <span style="
                    background-color: {{ ($item->status ?? 'completed') === 'completed' ? '#dcfce7' : '#fef3c7' }};
                    color: {{ ($item->status ?? 'completed') === 'completed' ? '#166534' : '#92400e' }};
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-size: 10px;
                ">
                    {{ ucfirst($item->status ?? 'Completed') }}
                </span>
            </td>
            @if(isset($showProject) && $showProject)
            <td>{{ $item->project->name ?? 'N/A' }}</td>
            @endif
        </tr>
        @empty
        <tr>
            <td colspan="{{ isset($showProject) && $showProject ? '7' : '6' }}" class="text-center">No records found</td>
        </tr>
        @endforelse
    </tbody>
    @if($data->count() > 0)
    <tfoot>
        <tr class="total-row">
            <td colspan="{{ isset($showProject) && $showProject ? '4' : '4' }}"><strong>Total Amount:</strong></td>
            <td class="text-right amount">
                <strong>RWF {{ number_format($data->sum(function($item) { return $item->amount ?? $item->amount_received ?? 0; }), 0) }}</strong>
            </td>
            <td colspan="{{ isset($showProject) && $showProject ? '2' : '1' }}"></td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection