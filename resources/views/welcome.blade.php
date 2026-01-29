@extends('layouts.guest')

@section('title', 'Welcome')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">

                <h1 class="fw-bold mb-3">
                    💬 Live Chat System
                </h1>

                <p class="text-muted mb-4">
                    Realtime chat antara Admin dan Customer menggunakan Laravel 11 & WebSocket
                </p>

                <div class="d-flex justify-content-center gap-3 mb-5">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            Register
                        </a>
                    @endauth
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5>⚡ Realtime</h5>
                                <p class="text-muted small">
                                    Chat instan dengan WebSocket & Laravel Reverb
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5>🔐 Secure Auth</h5>
                                <p class="text-muted small">
                                    Login & register aman dengan Laravel Auth
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5>💻 Admin & Customer</h5>
                                <p class="text-muted small">
                                    Sistem role terpisah admin dan customer
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
