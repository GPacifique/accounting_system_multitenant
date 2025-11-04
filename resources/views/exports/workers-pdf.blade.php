{{-- resources/views/exports/workers-pdf.blade.php --}}
@extends('exports.pdf-template')

@section('content')
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Position</th>
            <th>Contact</th>
            <th>Daily Rate</th>
            <th>Status</th>
            <th>Hired Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $worker)
        <tr>
            <td>{{ $worker->id }}</td>
            <td>{{ $worker->name ?? 'N/A' }}</td>
            <td>{{ $worker->position ?? 'N/A' }}</td>
            <td>{{ $worker->contact ?? 'N/A' }}</td>
            <td class="text-right amount">RWF {{ number_format($worker->daily_rate ?? 0, 0) }}</td>
            <td class="text-center">
                <span style="
                    background-color: {{ ($worker->status ?? 'active') === 'active' ? '#dcfce7' : '#fef3c7' }};
                    color: {{ ($worker->status ?? 'active') === 'active' ? '#166534' : '#92400e' }};
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-size: 10px;
                ">
                    {{ ucfirst($worker->status ?? 'Active') }}
                </span>
            </td>
            <td class="text-center">{{ $worker->created_at->format('Y-m-d') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">No workers found</td>
        </tr>
        @endforelse
    </tbody>
    @if($data->count() > 0)
    <tfoot>
        <tr class="total-row">
            <td colspan="4"><strong>Total Workers:</strong></td>
            <td class="text-center"><strong>{{ $data->count() }}</strong></td>
            <td colspan="2">
                <strong>Active: {{ $data->where('status', 'active')->count() }}</strong>
            </td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection