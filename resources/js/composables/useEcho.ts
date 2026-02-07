import {
    configureEcho,
    echo,
    useConnectionStatus,
    useEcho,
    useEchoPublic,
    useEchoPresence,
    useEchoModel,
} from '@laravel/echo-vue';

export function setupEcho(): void {
    const port = Number(import.meta.env.VITE_REVERB_PORT ?? 443);

    configureEcho({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: port,
        wssPort: port,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
    });

    // Eagerly create the Echo/Pusher instance to establish the WebSocket connection
    // immediately, rather than waiting for a composable to trigger it lazily.
    echo();
}

export { echo, useConnectionStatus, useEcho, useEchoPublic, useEchoPresence, useEchoModel };
