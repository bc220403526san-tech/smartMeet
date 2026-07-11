import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// FIX: fall back to window.location.hostname so this works from ANY
// device that can reach the app, instead of a single hardcoded LAN IP.
// If VITE_REVERB_HOST is explicitly set (e.g. to 10.15.175.20), it is
// used; otherwise whatever host the page itself was loaded from is used.
const wsHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const wsPort = import.meta.env.VITE_REVERB_PORT ?? 8080;
const scheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

const echo = new Echo({
    broadcaster: 'reverb',

    key: import.meta.env.VITE_REVERB_APP_KEY,

    wsHost: wsHost,

    wsPort: wsPort,

    wssPort: wsPort,

    forceTLS: scheme === 'https',

    enabledTransports: ['ws', 'wss'],

    authEndpoint: '/broadcasting/auth',

    withCredentials: true,
});

window.Echo = echo;

export default echo;
