@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Financial Reports</h1>
            <p class="text-muted">Revenue overview for the last 30 days.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>Revenue by Type</h5>
                    @if(isset($byType) && $byType->count())
                        <ul class="list-group list-group-flush">
                            @foreach($byType as $type => $amount)
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>{{ ucfirst($type) }}</div>
                                    <div>{{ currency($amount) }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No revenue by type available.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>Top Paying Members</h5>
                    @if(isset($topMembers) && $topMembers->count())
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Member</th><th class="text-end">Amount</th></tr>
                            </thead>
                            <tbody>
                                @foreach($topMembers as $m)
                                    <tr>
                                        <td>{{ $m->member?->full_name ?? 'Member #'.$m->member_id }}</td>
                                        <td class="text-end">{{ currency($m->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">No top members data.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const revenueLabels = {!! json_encode($labels ?? []) !!};
    const revenueData = {!! json_encode($data ?? []) !!};

    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenue',
                data: revenueData,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.12)',
                fill: true,
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { display: true },
                y: { display: true }
            }
        }
    });
</script>
@endpush

@endsection
