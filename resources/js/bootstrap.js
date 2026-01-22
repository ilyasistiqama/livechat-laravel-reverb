import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

// 🔥 WAJIB
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb', // ⬅️ SERVER = REVERB
    key: 'local',
    wsHost: '127.0.0.1',
    wsPort: 8080,
    forceTLS: false,
    disableStats: true,
});
