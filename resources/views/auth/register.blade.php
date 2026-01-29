@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h4 class="text-center fw-bold mb-4">
                            📝 Register
                        </h4>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            {{-- NAME --}}
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required>

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- EMAIL --}}
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    required>

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

                            {{-- CONFIRM PASSWORD --}}
                            <div class="mb-4">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <button class="btn btn-primary w-100">
                                Register
                            </button>
                        </form>

                    </div>
                </div>

                <div class="text-center mt-3">
                    <small>
                        Sudah punya akun?
                        <a href="{{ route('login') }}">Login</a>
                    </small>
                </div>

            </div>
        </div>
    </div>
@endsection
