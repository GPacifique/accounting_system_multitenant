@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg p-6 flex flex-col">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-green-700">Manager</h2>
            <p class="text-sm text-gray-500 mt-1">{{ Auth::user()->name ?? 'Manager' }}</p>
        </div>
        <nav class="flex-1">
            <ul class="space-y-4">
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-green-100 text-green-700 font-semibold">Dashboard</a></li>
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-green-100">Projects</a></li>
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-green-100">Expenses</a></li>
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-green-100">Reports</a></li>
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-green-100">Team</a></li>
            </ul>
        </nav>
        <div class="mt-8">
            <a href="{{ url('/') }}" class="block w-full text-center py-2 bg-red-500 text-white rounded hover:bg-red-600">Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white text-center">
                <h3>Manager Dashboard</h3>
            </div>
            <div class="card-body text-center">
                <p class="text-muted">Here you can oversee projects, track expenses, and monitor staff performance.</p>

                <div class="row mt-4">
                    <!-- Projects -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Projects</h5>
                                <p class="card-text">View and manage all active construction projects.</p>
                                <a href="#" class="btn btn-outline-success">View Projects</a>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Expenses</h5>
                                <p class="card-text">Track expenses and vendor payments for each project.</p>
                                <a href="#" class="btn btn-outline-success">View Expenses</a>
                            </div>
                        </div>
                    </div>

                    <!-- Reports -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Reports</h5>
                                <p class="card-text">Generate financial and progress reports.</p>
                                <a href="#" class="btn btn-outline-success">Generate Reports</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
