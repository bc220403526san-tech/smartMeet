import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const pageIsHttps = window.location.protocol === 'https';

const wsHost =
    import.meta.env.VITE_REVERB_HOST ||
    window.location.hostname;

// Production HTTPS:
// Browser -> WSS 443 -> Nginx -> Reverb 127.0.0.1:8080
//
// Local HTTP:
// Browser -> WS -> configured Reverb port / 8080
const configuredPort = Number(
    import.meta.env.VITE_REVERB_PORT || 8080
);

const wsPort = pageIsHttps ? 443 : configuredPort;
const wssPort = 443;

const echo = new Echo({
    broadcaster: 'reverb',

    key: import.meta.env.VITE_REVERB_APP_KEY,

    wsHost: wsHost,

    wsPort: wsPort,
    wssPort: wssPort,

    forceTLS: pageIsHttps,

    enabledTransports: pageIsHttps
        ? ['wss']
        : ['ws'],

    authEndpoint: '/broadcasting/auth',

    withCredentials: true,
});

window.Echo = echo;

export default echo;
