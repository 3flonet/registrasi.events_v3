/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

if (window.PusherConfig) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: window.PusherConfig.key,
        cluster: window.PusherConfig.cluster,
        wsHost: window.PusherConfig.host,
        wsPort: window.PusherConfig.port,
        wssPort: window.PusherConfig.port,
        forceTLS: window.PusherConfig.scheme === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}
