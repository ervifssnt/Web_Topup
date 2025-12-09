<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Game Top Up')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: #111;
            color: white;
        }
        header a {
            color: #ffcc00;
            text-decoration: none;
            margin-left: 10px;
        }
        header form {
            display: inline;
        }
        header button {
            background: none;
            border: none;
            color: #ffcc00;
            cursor: pointer;
            font-size: 16px;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
    @yield('styles')
</head>
<body>
    <header>
        <h1>Game Topup</h1>
        <div>
            @auth
                Welcome, {{ Auth::user()->username }} | 
                Balance: Rp {{ number_format(Auth::user()->balance, 2) }} |
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a> | 
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </header>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>