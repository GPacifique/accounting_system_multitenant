{{-- resources/views/tenant/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', $tenant->name . ' Dashboard | SiteLedger')
@section('meta_description', 'Business dashboard for ' . $tenant->name . ' - View projects, tasks, and team performance in your SiteLedger workspace.')
@section('meta_keywords', 'business dashboard, project management, task tracking, team analytics, business insights')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Tenant Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                {{-- Tenant Avatar --}}
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-xl">
                        {{ strtoupper(substr($tenant->name, 0, 2)) }}
                    </span>
                </div>
                
                {{-- Tenant Info --}}
                <div>
                    <h1 class="text-3xl font-bold">{{ $tenant->name }}</h1>
                    <p class="text-blue-100 mt-1">{{ $tenant->getBusinessTypeLabel() }}</p>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-blue-200">
                        <span>{{ $tenant->domain }}</span>
                        <span>•</span>
                        <span>{{ $stats['users_count'] }} {{ Str::plural('user', $stats['users_count']) }}</span>
                        @if($tenant->subscription_plan)
                            <span>•</span>
                            <span class="px-2 py-1 bg-white/20 rounded">{{ $tenant->getSubscriptionPlanLabel() }}</span>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Quick Actions --}}
            <div class="flex space-x-3">
                <a href="{{ route('projects.create') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">
                    <i class="fas fa-plus mr-2"></i>
                    New Project
                </a>
                <a href="{{ route('tasks.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-400 transition">
                    <i class="fas fa-tasks mr-2"></i>
                    New Task
                </a>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Projects --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Projects</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['projects_count'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-project-diagram text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('projects.index') }}" class="text-blue-600 text-sm hover:underline">
                    View all projects →
                </a>
            </div>
        </div>

        {{-- Active Tasks --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Tasks</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['active_tasks'] }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-tasks text-orange-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('tasks.index', ['status' => 'active']) }}" class="text-orange-600 text-sm hover:underline">
                    View active tasks →
                </a>
            </div>
        </div>

        {{-- Total Tasks --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['tasks_count'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('tasks.index') }}" class="text-green-600 text-sm hover:underline">
                    View all tasks →
                </a>
            </div>
        </div>

        {{-- Team Members --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Team Members</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['users_count'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @if(Auth::user()->isAdminForTenant($tenant->id) || Auth::user()->isSuperAdmin())
                    <a href="{{ route('admin.tenants.users', $tenant) }}" class="text-purple-600 text-sm hover:underline">
                        Manage team →
                    </a>
                @else
                    <span class="text-gray-400 text-sm">Team overview</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Recent Tasks --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Tasks</h3>
                <a href="{{ route('tasks.index') }}" class="text-blue-600 text-sm hover:underline">
                    View all
                </a>
            </div>
            
            @if($recentTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTasks as $task)
                        <div class="flex items-center space-x-4 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            {{-- Priority Badge --}}
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                                       ($task->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                                        ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                            
                            {{-- Task Info --}}
                            <div class="flex-grow min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $task->title }}</h4>
                                <p class="text-xs text-gray-500">
                                    {{ $task->project ? $task->project->name : 'No Project' }}
                                    @if($task->assignedUser)
                                        • Assigned to {{ $task->assignedUser->name }}
                                    @endif
                                </p>
                            </div>
                            
                            {{-- Status --}}
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-600 mb-2">No Tasks Yet</h4>
                    <p class="text-gray-500 mb-4">Create your first task to get started</p>
                    <a href="{{ route('tasks.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>
                        Create Task
                    </a>
                </div>
            @endif
        </div>

        {{-- Recent Projects --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Projects</h3>
                <a href="{{ route('projects.index') }}" class="text-blue-600 text-sm hover:underline">
                    View all
                </a>
            </div>
            
            @if($recentProjects->count() > 0)
                <div class="space-y-4">
                    @foreach($recentProjects as $project)
                        <div class="flex items-center space-x-4 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            {{-- Project Icon --}}
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-project-diagram text-white"></i>
                                </div>
                            </div>
                            
                            {{-- Project Info --}}
                            <div class="flex-grow min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $project->name }}</h4>
                                <p class="text-xs text-gray-500">
                                    {{ $project->client ? $project->client->name : 'No Client' }}
                                    • Updated {{ $project->updated_at->diffForHumans() }}
                                </p>
                            </div>
                            
                            {{-- Status --}}
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $project->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($project->status === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-project-diagram text-4xl text-gray-300 mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-600 mb-2">No Projects Yet</h4>
                    <p class="text-gray-500 mb-4">Create your first project to get started</p>
                    <a href="{{ route('projects.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-plus mr-2"></i>
                        Create Project
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Quick Actions</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('projects.create') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-project-diagram text-blue-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-900">New Project</span>
            </a>
            
            <a href="{{ route('tasks.create') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-orange-50 hover:border-orange-300 transition">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-tasks text-orange-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-900">New Task</span>
            </a>
            
            <a href="{{ route('incomes.create') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-900">Add Income</span>
            </a>
            
            <a href="{{ route('expenses.create') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-300 transition">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-minus-circle text-red-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-900">Add Expense</span>
            </a>
        </div>
    </div>
</div>

{{-- Tenant Context Script --}}
<script>
// Store current tenant context in session storage
sessionStorage.setItem('currentTenantId', '{{ $tenant->id }}');
sessionStorage.setItem('currentTenantName', '{{ $tenant->name }}');

// Show tenant context indicator
document.addEventListener('DOMContentLoaded', function() {
    const tenantIndicator = document.createElement('div');
    tenantIndicator.className = 'fixed bottom-4 left-4 bg-blue-600 text-white px-3 py-2 rounded-lg shadow-lg text-sm z-50';
    tenantIndicator.innerHTML = `
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
            <span>Working in: {{ $tenant->name }}</span>
        </div>
    `;
    document.body.appendChild(tenantIndicator);
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        tenantIndicator.style.transition = 'opacity 0.5s';
        tenantIndicator.style.opacity = '0';
        setTimeout(() => {
            if (tenantIndicator.parentNode) {
                tenantIndicator.parentNode.removeChild(tenantIndicator);
            }
        }, 500);
    }, 3000);
});
</script>
@endsection