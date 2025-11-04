<nav class="theme-aware-bg-card border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <a href="{{ url('/') }}" class="flex items-center text-lg font-semibold theme-aware-text">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>
            <div class="flex items-center">
                @auth
                    <span class="text-sm mr-4">Hi, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900 mr-4">Login</a>
                    <a href="{{ route('register') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
