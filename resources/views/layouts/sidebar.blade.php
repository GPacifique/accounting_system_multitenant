<!-- resources/views/layouts/sidebar.blade.php -->
<aside class="w-64 bg-green-800 text-green-100 min-h-screen flex flex-col">
      <img src="{{ asset('images/logo.png') }}" alt="BuildMate Logo" class="h-10 w-auto">
    <div class="p-4 text-lg font-bold border-b border-gray-700">
        BuildMate
    </div>
@vite(['resources/css/app.css', 'resources/js/app.js'])

    <nav class="flex-1 px-2 py-4 space-y-2">
        <!-- Dashboard -->


        <!-- Projects -->
        <a href="{{ route('projects.index') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('projects.*') ? 'bg-gray-900' : '' }}">
            Projects
        </a>

        <!-- Employees -->
        <a href="{{ route('employees.index') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employees.*') ? 'bg-gray-900' : '' }}">
            Employees
        </a>

        <!-- Expenses -->
        <a href="{{ route('expenses.index') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('expenses.*') ? 'bg-gray-900' : '' }}">
            Expenses
        </a>

        <!-- Reports -->
        <a href="{{ route('incomes.index') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('reports.*') ? 'bg-gray-900' : '' }}">
            incomes
        </a>
        <!-- Transactions-->
        <a href="{{ route('transactions.index') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('incomes.*') ? 'bg-gray-900' : '' }}">
            Transactions
        </a>
        <!-- Reports -->
        <a href="{{ route('reports.index') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('incomes.*') ? 'bg-gray-900' : '' }}">
        Reports
        </a>
<!-- Reports -->
        <a href="{{ route('users.index') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('incomes.*') ? 'bg-gray-900' : '' }}">
        Users
        </a>
        <!-- Settings -->
        <a href="{{ route('settings.index') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('settings') ? 'bg-gray-900' : '' }}">
            Settings
        </a>
    </nav>

    <div class="p-4 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="w-full text-left px-4 py-2 rounded hover:bg-gray-700">
                Logout
            </button>
        </form>
    </div>
</aside>
