@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
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
                @include('admin.inbox-list')
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

                        <a href="{{ route('chat.index') }}"
                            class="btn btn-sm btn-outline-primary rounded-pill">
                            Chat
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- FLOATING CHAT --}}
        <a href="{{ route('chat.index', ['type' => 'customer-to-admin']) }}" class="chat-float-btn" title="Chat Customer Service">
            <i class="bi bi-chat-dots-fill"></i>
        </a>
    @endif
@endsection
