<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: var(--bg-gradient-primary);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-primary);
        }

        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        .auth-container {
            background: var(--bg-card-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow-glass);
            text-align: center;
            border: 1px solid var(--border-secondary);
        }

        .auth-container h2 {
            margin-bottom: 1.5rem;
            color: var(--text-primary);
            font-size: 1.8rem;
        }

        .auth-container form {
            display: flex;
            flex-direction: column;
        }

        .auth-container input {
            padding: 0.9rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border-secondary);
            border-radius: 12px;
            background: var(--bg-input);
            box-shadow: var(--shadow-inset);
            font-size: 1rem;
            color: var(--text-primary);
        }

        .auth-container input:focus {
            outline: none;
            box-shadow: 0 0 0 3px var(--focus-ring);
            border-color: var(--border-focus);
        }

        .auth-container button {
            padding: 0.9rem;
            border: none;
            border-radius: 12px;
            background: var(--bg-primary-button);
            color: var(--text-button);
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .auth-container button:hover {
            background: var(--bg-primary-button-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .auth-links {
            margin-top: 1rem;
        }

        .auth-links a {
            text-decoration: none;
            color: var(--text-link);
            font-size: 0.9rem;
        }

        .auth-links a:hover {
            text-decoration: underline;
            color: var(--text-link-hover);
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 2rem 1.5rem;
                border-radius: 16px;
            }
        }
    </style>

    <div class="auth-container">
        <h2>{{ __('Login to Your Account') }}</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                              type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded theme-aware-border text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm theme-aware-text-secondary">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-6">
                @if (Route::has('password.request'))
                    <a class="auth-links underline text-sm theme-aware-text-secondary hover:theme-aware-text" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="ms-3">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
