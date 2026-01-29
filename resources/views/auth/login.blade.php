@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h4 class="text-center fw-bold mb-4">
                            🔐 Login
                        </h4>

                        {{-- SESSION STATUS --}}
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            {{-- EMAIL --}}
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    required autofocus>

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PASSWORD --}}
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- REMEMBER --}}
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="small">
                                        Forgot password?
                                    </a>
                                @endif

                                <button class="btn btn-primary px-4">
                                    Login
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="text-center mt-3">
                    <small>
                        Belum punya akun?
                        <a href="{{ route('register') }}">Register</a>
                    </small>
                </div>

            </div>
        </div>
    </div>
@endsection
