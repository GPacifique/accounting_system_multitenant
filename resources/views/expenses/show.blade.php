@extends('layouts.app')
@include('layouts.sidebar')
@section('content')
<h1 class="text-2xl font-bold mb-4">Expense Details</h1>

<p><strong>Title:</strong> {{ $expense->title }}</p>
<p><strong>Amount:</strong> {{ $expense->amount }}</p>
<p><strong>Date:</strong> {{ $expense->date }}</p>
<p><strong>Description:</strong> {{ $expense->description }}</p>

<a href="{{ route('expenses.index') }}" class="mt-4 inline-block bg-gray-500 text-white px-4 py-2 rounded">Back</a>
@endsection
@section('scripts')
<script>
    // You can add any specific scripts for this page here      
    @section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- Bar Chart: Projects vs Expenses ---
    const barCtx = document.getElementById('barChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Projects',
                        data: @json($projectsData ?? [5, 10, 8, 12, 6, 9]),
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    },
                    {
                        label: 'Expenses',
                        data: @json($expensesData ?? [1000, 1500, 800, 1200, 600, 900]),
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // --- Pie Chart: Employee Distribution ---
    const pieCtx = document.getElementById('pieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: @json($departments ?? ['HR', 'IT', 'Finance', 'Marketing']),
                datasets: [{
                    data: @json($employeeDistribution ?? [10, 20, 15, 5]),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(249, 115, 22, 0.7)',
                        'rgba(168, 85, 247, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
</script>
@endsection
</script>