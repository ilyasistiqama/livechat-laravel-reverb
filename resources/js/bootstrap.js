import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;

axios.defaults.withCredentials = true;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',

    key: 'local',
    wsHost: '127.0.0.1',
    wsPort: 8080,

    forceTLS: false,
    disableStats: true,

    authEndpoint: '/broadcasting/auth',
    withCredentials: true,
});
