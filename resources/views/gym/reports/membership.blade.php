@extends('layouts.app')

@section('title', 'Membership Reports')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Membership Reports</h1>
            <p class="text-muted">New signups and membership-type breakdown over the last 30 days.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5>New Signups (30d)</h5>
                    <canvas id="membersChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5>By Membership Type</h5>
                    @if(isset($byType) && $byType->count())
                        <ul class="list-group list-group-flush">
                            @foreach($byType as $type => $count)
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>{{ ucfirst($type) }}</div>
                                    <div>{{ $count }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No membership breakdown available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Membership Status</h5>
                    <p class="mb-0">Active: <strong>{{ $active ?? 0 }}</strong> — Expired: <strong>{{ $expired ?? 0 }}</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const memberLabels = {!! json_encode($labels ?? []) !!};
    const memberData = {!! json_encode($data ?? []) !!};

    new Chart(document.getElementById('membersChart'), {
        type: 'bar',
        data: {
            labels: memberLabels,
            datasets: [{
                label: 'New Members',
                data: memberData,
                backgroundColor: 'rgba(40,167,69,0.6)'
            }]
        },
        options: { responsive: true }
    });
</script>
@endpush

@endsection

