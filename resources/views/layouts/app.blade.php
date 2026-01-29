<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $user = auth('admin')->user() ?? auth('member')->user();
    @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-id" content="{{ $user?->id }}">

    <title>@yield('title', 'Dashboard')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    @stack('styles')
</head>

<body class="bg-light">

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">MyApp</a>

            @if ($user)
                <div class="ms-auto d-flex align-items-center gap-3">
                    <span class="text-muted small">
                        {{ $user->name }}
                    </span>

                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                        class="btn btn-sm btn-outline-danger">
                        Logout
                    </a>
                </div>
            @endif
        </div>
    </nav>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
        @csrf
    </form>

    {{-- CONTENT --}}
    <main class="container py-4">
        @yield('content')
    </main>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
