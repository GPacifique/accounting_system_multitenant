<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymMaster - Fitness Club Login</title>
    <meta name="description" content="Sign in to GymMaster to book classes, track workouts, and manage your fitness journey online.">
    <meta name="keywords" content="gym, fitness, club, membership, classes, workout, health, login, exercise">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph -->
    <meta property="og:title" content="GymMaster - Fitness Club & Workout Tracker">
    <meta property="og:description" content="Access your personalized dashboard, book sessions, and manage your gym membership online with GymMaster.">
    <meta property="og:image" content="{{ asset('images/logo-gym-ms.svg') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="GymMaster - Fitness Club & Gym Management">
    <meta name="twitter:description" content="Join GymMaster to track your workouts, book classes, and manage your gym experience all in one place.">
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: url('{{ asset('images/background-gym.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.97);
            padding: 2.2rem;
            border-radius: 16px;
            width: 370px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.13);
            text-align: center;
        }

        .auth-container img.logo {
            width: 106px;
            margin-bottom: 1rem;
        }

        .auth-container h2 {
            margin-bottom: 0.7rem;
            color: #059669;
        }

        .gym-benefits {
            font-size: 1rem;
            text-align: left;
            margin: 0.8rem 0 1.2rem;
            color: #4B5563;
        }
        .gym-benefits ul {
            list-style: none;
            padding: 0;
        }
        .gym-benefits li {
            margin: 0.3rem 0;
            padding-left: 1.2em;
            text-indent: -1em;
        }

        .auth-container form {
            display: flex;
            flex-direction: column;
        }

        .auth-container input {
            padding: 0.83rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 1rem;
        }

        .auth-container input:focus {
            outline: none;
            border-color: #059669;
            box-shadow: 0 0 0 2px rgba(5,150,105,0.11);
        }

        .auth-container button {
            padding: 0.85rem;
            border: none;
            border-radius: 7px;
            background-color: #059669;
            color: white;
            font-size: 1.02rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }

        .auth-container button:hover {
            background-color: #047857;
        }

        .auth-links {
            margin-top: 1.2rem;
        }

        .auth-links a {
            text-decoration: none;
            color: #059669;
            margin: 0 0.6rem;
            font-size: 0.94rem;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        @media(max-width: 400px) {
            .auth-container {
                width: 96%;
                padding: 1.6rem;
            }
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <!-- Logo -->
        <img src="{{ asset('images/logo-gym-ms.svg') }}" alt="gym ms Logo" class="logo">

        <h2>Welcome to GymMaster</h2>
        <div class="gym-benefits">
            <ul>
                <li>🏋️ Track your workouts & progress</li>
                <li>🧘 Book group classes instantly</li>
                <li>💪 Manage your gym membership online</li>
                <li>📆 Reserve equipment or sessions</li>
            </ul>
        </div>
        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <!-- Links -->
        <div class="auth-links">
            <a href="{{ route('register') }}">Register</a> |
            <a href="{{ route('password.request') }}">Forgot Password?</a>
        </div>
    </div>

</body>
</html>
