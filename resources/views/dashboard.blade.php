@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @vite(['resources/js/admin-inbox.js'])
    <style>
        .chat-float-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #0d6efd;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .25);
            z-index: 9999;
        }

        .chat-float-btn:hover {
            background: #0b5ed7;
            color: #fff;
        }
    </style>

    @php
        use App\Services\AuthResolver;
        $auth = AuthResolver::resolve();
    @endphp

    {{-- ================= ADMIN ================= --}}
    @if ($auth && $auth->guard === 'admin')
        <div class="card shadow-sm">
            <div class="card-body">
                <meta name="auth-id" content="{{ auth()->id() }}">

                <style>
                    .inbox-item {
                        transition: background .15s ease;
                    }

                    .inbox-item:hover {
                        background: #f8f9fa;
                    }
                </style>


                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            📥 Inbox Customer
                        </h5>

                        <span class="badge bg-primary" id="inbox-count" style="display:none">
                            0
                        </span>
                    </div>

                    <div class="card-body p-0">

                        {{-- LIST INBOX --}}
                        <ul id="inbox-list" class="list-group list-group-flush">
                            {{-- realtime injected --}}
                        </ul>

                        {{-- EMPTY STATE --}}
                        <div id="inbox-empty" class="text-center text-muted py-4">
                            Belum ada pesan masuk
                        </div>

                    </div>
                </div>

                <audio id="inbox-sound" src="{{ asset('wewokdetok.mp3') }}"></audio>

            </div>
        </div>

        {{-- ================= MEMBER ================= --}}
    @elseif ($auth && $auth->guard === 'member')
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="mb-1">Halo {{ $auth->user->name }} 👋</h5>
                <p class="text-muted mb-0">
                    Silakan hubungi admin atau customer lain jika butuh bantuan
                </p>
            </div>
        </div>

        {{-- TESTIMONI --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">💬 Testimoni Customer</h6>

                @foreach ([2 => 'Andi Pratama', 3 => 'Siti Rahma', 4 => 'Budi Santoso'] as $id => $name)
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="d-flex">
                            <img src="https://picsum.photos/seed/user{{ $id }}/50" class="rounded-circle me-3"
                                width="50" height="50">

                            <div>
                                <strong>{{ $name }}</strong>
                                <p class="mb-1 text-muted small">
                                    Pelayanan sangat membantu dan mudah digunakan.
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('chat.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            Chat
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- FLOATING CHAT --}}
        <a href="{{ route('chat.index', ['type' => 'customer-to-admin']) }}" class="chat-float-btn"
            title="Chat Customer Service">
            <i class="bi bi-chat-dots-fill"></i>
        </a>
    @endif
@endsection
