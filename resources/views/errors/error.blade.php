<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            text-align: center;
        }

        .container {
            max-width: 600px;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            font-size: 3rem;
            margin: 0;
        }

        p {
            font-size: 1.2rem;
            color: #555;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>@yield('code')</h1>
        <p>{{ session('error') }}</p>
        {{-- <a href="{{ url('/') }}">Go to Home</a> --}}
        @auth
            @if (auth()->user()->role_id && auth()->user()->roleFor() == 'is_admin')
                <a href="{{ route('admin.dashboard') }}">Go to Home</a>
            @else
                <a href="{{ route('sdcsmuser.dashboard') }}">Go to Home</a>
            @endif
        @endauth
    </div>
</body>

</html>
