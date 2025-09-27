@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Workers</h1>

    <a href="{{ route('workers.create') }}" class="btn btn-primary mb-3">New Worker</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Position</th>
                <th>Salary</th>
                <th>Hired</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($workers as $worker)
                <tr>
                    <td>{{ $worker->id }}</td>
                    <td>{{ $worker->full_name ?? "{$worker->first_name} {$worker->last_name}" }}</td>
                    <td>{{ $worker->position }}</td>
                    <td>{{ number_format(($worker->salary_cents ?? 0) / 100, 2) }} {{ $worker->currency }}</td>
                    <td>{{ optional($worker->hired_at)->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('workers.show', $worker) }}" class="btn btn-sm btn-outline-primary">View</a>
                        <a href="{{ route('workers.edit', $worker) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('workers.destroy', $worker) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $workers->links() }}
</div>
@endsection
