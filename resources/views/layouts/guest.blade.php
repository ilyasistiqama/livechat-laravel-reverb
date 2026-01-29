<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Welcome')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @stack('styles')
</head>

<body class="bg-light">

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                LiveChat
            </a>

            <div class="ms-auto d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- CONTENT --}}
    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
