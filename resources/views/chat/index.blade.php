<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="auth-id" content="{{ auth()->id() }}">
    <title>Live Chat</title>

    @vite(['resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <audio id="notif-sound" src="{{ asset('wewokdetok.mp3') }}"></audio>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f2f5;
        }

        .card {
            max-width: 600px;
            margin: auto;
            border-radius: 15px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            font-weight: 500;
        }

        #chat-box {
            height: 400px;
            overflow-y: auto;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 0 0 0 0;
        }

        .msg {
            padding: 10px 15px;
            border-radius: 20px;
            margin-bottom: 10px;
            max-width: 75%;
            font-size: 14px;
            word-wrap: break-word;
            position: relative;
        }

        .msg.me {
            background-color: #0d6efd;
            color: #fff;
            margin-left: auto;
            text-align: right;
            border-bottom-right-radius: 2px;
        }

        .msg.them {
            background-color: #e4e6eb;
            color: #333;
            margin-right: auto;
            text-align: left;
            border-bottom-left-radius: 2px;
        }

        .msg.me.read::after {
            content: "✓✓";
            font-size: 10px;
            margin-left: 6px;
            color: #cce;
        }

        #typing-indicator {
            font-size: 13px;
            color: #666;
            font-style: italic;
            margin-top: 5px;
        }

        .card-footer {
            background-color: #fff;
            border-top: 1px solid #ddd;
        }

        #chat-form input {
            border-radius: 20px;
        }

        #chat-form button {
            border-radius: 20px;
        }

        #reset-chat {
            min-width: 120px;
            border-radius: 20px;
        }

        #user-status {
            font-size: 14px;
        }

        .online::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #28a745;
            border-radius: 50%;
            margin-right: 5px;
        }

        .avatar {
            width: 35px;
            height: 35px;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="card shadow-sm">
            <!-- HEADER -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    ({{ auth()->user()->role }}) Chat dengan
                    <strong id="user-{{ $toUser->id }}">{{ $toUser->name }}</strong>
                    <span id="badge-{{ $toUser->id }}" class="badge bg-danger ms-1 d-none">0</span>
                </div>
                <div id="user-status" class="text-success">
                    <span class="online"></span> Online
                </div>
            </div>

            <!-- CHAT BOX -->
            <div class="card-body p-3" id="chat-box"></div>

            <!-- Typing indicator -->
            <div id="typing-indicator" class="p-2" style="display: none;">sedang mengetik...</div>

            <!-- FOOTER: FORM & RESET -->
            <div class="card-footer p-3 d-flex justify-content-between align-items-center gap-2 flex-wrap">
                <form id="chat-form" class="d-flex gap-2 flex-grow-1 mb-2 mb-md-0">
                    @csrf
                    <input type="hidden" id="to_id" value="{{ $toUser->id }}">
                    <input type="text" id="message" class="form-control" placeholder="Ketik pesan..."
                        autocomplete="off">
                    <button type="submit" class="btn btn-primary">Kirim</button>
                    @if (auth()->user()->role === 'admin')
                        <button id="reset-chat" class="btn btn-danger">Chat Selesai</button>
                    @endif
                </form>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
