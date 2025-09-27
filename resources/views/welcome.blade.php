<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | BuilMate</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: url('{{ asset('images/background.png') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-align: center;
        }

        .auth-container img.logo {
            width: 100px;
            margin-bottom: 1rem;
        }

        .auth-container h2 {
            margin-bottom: 1rem;
            color: #2563eb;
        }

        .auth-container form {
            display: flex;
            flex-direction: column;
        }

        .auth-container input {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 1rem;
        }

        .auth-container input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.2);
        }

        .auth-container button {
            padding: 0.8rem;
            border: none;
            border-radius: 6px;
            background-color: #2563eb;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .auth-container button:hover {
            background-color: #1e40af;
        }

        .auth-links {
            margin-top: 1rem;
        }

        .auth-links a {
            text-decoration: none;
            color: #2563eb;
            margin: 0 0.5rem;
            font-size: 0.9rem;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        @media(max-width: 400px) {
            .auth-container {
                width: 90%;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <!-- Logo -->
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">

        <h2>Welcome to BuildMate</h2>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- Links -->
        <div class="auth-links">
            <a href="{{('register')}}">Register</a> |
            <a href="{{ route('password.request') }}">Forgot Password?</a>
        </div>
    </div>

</body>
</html>
