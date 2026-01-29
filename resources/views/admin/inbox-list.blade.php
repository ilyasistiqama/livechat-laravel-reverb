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
