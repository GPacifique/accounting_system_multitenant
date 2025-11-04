{{-- resources/views/exports/projects-pdf.blade.php --}}
@extends('exports.pdf-template')

@section('content')
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Project Name</th>
            <th>Client</th>
            <th>Contract Value</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $project)
        <tr>
            <td>{{ $project->id }}</td>
            <td>{{ $project->name }}</td>
            <td>{{ $project->client_name ?? 'N/A' }}</td>
            <td class="text-right amount">RWF {{ number_format($project->contract_value ?? 0, 0) }}</td>
            <td class="text-center">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : 'N/A' }}</td>
            <td class="text-center">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : 'N/A' }}</td>
            <td class="text-center">
                <span style="
                    background-color: {{ $project->status === 'completed' ? '#dcfce7' : ($project->status === 'active' ? '#dbeafe' : '#fef3c7') }};
                    color: {{ $project->status === 'completed' ? '#166534' : ($project->status === 'active' ? '#1e40af' : '#92400e') }};
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-size: 10px;
                ">
                    {{ ucfirst($project->status ?? 'N/A') }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">No projects found</td>
        </tr>
        @endforelse
    </tbody>
    @if($data->count() > 0)
    <tfoot>
        <tr class="total-row">
            <td colspan="3"><strong>Total Contract Value:</strong></td>
            <td class="text-right amount"><strong>RWF {{ number_format($data->sum('contract_value'), 0) }}</strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection