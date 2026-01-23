<meta name="auth-id" content="{{ auth()->id() }}">

<div class="bg-white shadow rounded-lg p-4">
    <h3 class="font-semibold mb-4">Inbox</h3>

    <ul id="inbox-list" class="space-y-2"></ul>
</div>

{{-- <audio id="inbox-sound" src="/sound/notif.mp3"></audio> --}}

<audio id="inbox-sound" src="{{ asset('wewokdetok.mp3') }}"></audio>
