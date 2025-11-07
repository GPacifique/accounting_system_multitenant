@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Attendance Reports</h1>
            <p class="text-muted">Daily check-ins for the last 30 days.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <canvas id="attendanceChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Recent Check-ins</h5>
                    @if(isset($recent) && $recent->count())
                        <ul class="list-group list-group-flush">
                            @foreach($recent as $r)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $r->member?->full_name ?? 'Unknown' }}</strong>
                                        <div class="small text-muted">{{ $r->member?->member_id ? 'Member ID: '.$r->member->member_id : '' }}</div>
                                    </div>
                                    <div class="text-end small text-muted">{{ optional($r->checked_in_at)->format('Y-m-d H:i') }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No recent check-ins.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const attendLabels = {!! json_encode($labels ?? []) !!};
    const attendData = {!! json_encode($data ?? []) !!};

    new Chart(document.getElementById('attendanceChart'), {
        type: 'line',
        data: {
            labels: attendLabels,
            datasets: [{
                label: 'Check-ins',
                data: attendData,
                borderColor: '#20c997',
                backgroundColor: 'rgba(32,201,151,0.12)',
                fill: true
            }]
        },
        options: { responsive: true }
    });
</script>
@endpush

@endsection

