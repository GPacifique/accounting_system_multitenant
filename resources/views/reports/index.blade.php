@extends('layouts.app')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Daily Worker Report</h2>

    <p class="mb-2">Date: <strong>{{ $date }}</strong></p>
    <p class="mb-2">Total Workers: <strong>{{ $totalWorkers }}</strong></p>
    <p class="mb-4">Total Daily Wages: <strong>RWF {{ number_format($totalDailyWages, 2) }}</strong></p>

    <div class="overflow-x-auto">
        <table class="w-full text-left border">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-2 px-3 border">Worker</th>
                    <th class="py-2 px-3 border">Task</th>
                    <th class="py-2 px-3 border">Daily Wage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-2 px-3">{{ $task->worker->name }}</td>
                    <td class="py-2 px-3">{{ $task->description }}</td>
                    <td class="py-2 px-3">RWF {{ number_format($task->daily_wage, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-4 text-center text-gray-500">No tasks for this day.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
