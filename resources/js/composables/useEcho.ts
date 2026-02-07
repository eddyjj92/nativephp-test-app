import { ref, type Ref } from 'vue';
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

// Module-level presence state — survives component mount/unmount cycles
let _presenceJoined = false;
const _onlineUserIds: Ref<Set<number>> = ref(new Set());

export function joinPresence(): void {
    if (!_presenceJoined) {
        echo()
            .join('online.users')
            .here((users: Array<{ id: number }>) => {
                _onlineUserIds.value = new Set(users.map((u) => u.id));
            })
            .joining((user: { id: number }) => {
                _onlineUserIds.value = new Set([..._onlineUserIds.value, user.id]);
            })
            .leaving((user: { id: number }) => {
                const next = new Set(_onlineUserIds.value);
                next.delete(user.id);
                _onlineUserIds.value = next;
            });
        _presenceJoined = true;
    }
}

export function leavePresence(): void {
    if (_presenceJoined) {
        echo().leave('online.users');
        _presenceJoined = false;
        _onlineUserIds.value = new Set();
    }
}

export function isPresenceJoined(): boolean {
    return _presenceJoined;
}

export function useOnlineUsers(): Ref<Set<number>> {
    return _onlineUserIds;
}

export { echo, useConnectionStatus, useEcho, useEchoPublic, useEchoPresence, useEchoModel };
