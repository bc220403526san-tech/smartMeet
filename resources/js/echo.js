import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'reverb',

    key: import.meta.env.VITE_REVERB_APP_KEY,

    wsHost: import.meta.env.VITE_REVERB_HOST,

    wsPort: import.meta.env.VITE_REVERB_PORT,

    wssPort: import.meta.env.VITE_REVERB_PORT,

    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',

    enabledTransports: ['ws', 'wss'],

    // 👇 IMPORTANT (missing in many setups)
    authEndpoint: '/broadcasting/auth',

    withCredentials: true,
});

window.Echo = echo;

export default echo;
