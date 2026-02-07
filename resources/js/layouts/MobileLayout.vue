<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onUnmounted, ref, watch } from 'vue';
import ConnectionError from '@/components/ConnectionError.vue';
import LocationSelectionModal from '@/components/LocationSelectionModal.vue';
import LoginModal from '@/components/LoginModal.vue';
import MobileBottomNav from '@/components/MobileBottomNav.vue';
import MobileTopBar from '@/components/MobileTopBar.vue';
import SearchModal from '@/components/SearchModal.vue';
import { useConnectionError } from '@/composables/useConnectionError';
import {
    isPresenceJoined,
    joinPresence,
    leavePresence,
    joinChatChannel,
    leaveChatChannel,
    onChatMessage,
    offChatMessage,
    useConnectionStatus,
    type ChatMessagePayload,
} from '@/composables/useEcho';
import type { AppPageProps } from '@/types';

type NavId = 'home' | 'catalog' | 'cart' | 'saved' | 'profile';

type Props = {
    activeNav?: NavId;
    showTopBar?: boolean;
    showChatButton?: boolean;
    showBottomBar?: boolean;
};

const page = usePage();
const manualLocationModal = ref(false);
const loginModalOpen = ref(false);
const chatLoginModalOpen = ref(false);
const searchModalOpen = ref(false);
const showLocationModal = computed(
    () =>
        (page.props.showLocationModal as boolean) || manualLocationModal.value,
);

const props = withDefaults(defineProps<Props>(), {
    activeNav: 'home',
    showTopBar: true,
    showChatButton: true,
    showBottomBar: true,
});

const topBarHeight = computed(() =>
    props.showTopBar ? 'calc(var(--inset-top, 0px) + 40px)' : '0px',
);

const { isOffline, errorMessage, retry } = useConnectionError();

// WebSocket: join presence channel when authenticated.
// State lives at module level (useEcho.ts) so it survives
// MobileLayout unmount/remount on Inertia navigations.
const authUser = computed(() => (page.props as AppPageProps).auth?.user);
const wsStatus = useConnectionStatus();

watch(
    authUser,
    (user) => {
        if (user) {
            joinPresence();
        } else {
            leavePresence();
        }
    },
    { immediate: true },
);

const presenceConnected = computed(
    () =>
        authUser.value &&
        isPresenceJoined() &&
        wsStatus.value === 'connected',
);

// Chat channel: separate from presence, delayed to avoid interference
watch(
    authUser,
    (user) => {
        if (user) {
            setTimeout(() => joinChatChannel(user.id), 1500);
        } else {
            leaveChatChannel();
        }
    },
    { immediate: true },
);

const wsUnreadExtra = ref(0);

function playNotificationSound(): void {
    try {
        const ctx = new AudioContext();
        const playTone = (freq: number, start: number, dur: number) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = freq;
            osc.type = 'sine';
            gain.gain.setValueAtTime(0.15, start);
            gain.gain.exponentialRampToValueAtTime(0.01, start + dur);
            osc.start(start);
            osc.stop(start + dur);
        };
        playTone(880, ctx.currentTime, 0.12);
        playTone(1100, ctx.currentTime + 0.12, 0.15);
    } catch {
        // Silent if audio not available
    }
}

function handleIncomingMessage(payload: ChatMessagePayload): void {
    if (payload.message.sender_id === authUser.value?.id) {
        return;
    }

    wsUnreadExtra.value++;
    playNotificationSound();
}

onChatMessage(handleIncomingMessage);

onUnmounted(() => {
    offChatMessage(handleIncomingMessage);
});

const unreadMessagesCount = computed(
    () =>
        ((page.props.unreadMessagesCount as number) ?? 0) +
        wsUnreadExtra.value,
);

const connectionException = computed(
    () => page.props.connection_exception as boolean,
);
const showConnectionError = computed(
    () => isOffline.value || connectionException.value,
);
</script>

<template>
    <div
        class="mobile-layout relative min-h-screen bg-slate-50 dark:bg-slate-900"
        :class="[
            props.showBottomBar
                ? 'pb-[calc(var(--inset-bottom,0px)+6rem)]'
                : 'pb-0',
            props.showTopBar ? 'pt-[var(--mobile-topbar-height)]' : 'pt-0',
        ]"
        :style="{ '--mobile-topbar-height': topBarHeight }"
    >
        <MobileTopBar
            v-if="props.showTopBar"
            @open-location="manualLocationModal = true"
            @open-search="searchModalOpen = true"
        />
        <div class="mobile-layout-content">
            <slot />
        </div>

        <!-- Floating Chat Button -->
        <Link
            v-if="props.showChatButton && authUser"
            href="/conversations"
            class="fixed right-[calc(var(--inset-right,0px)+1rem)] bottom-[calc(var(--inset-bottom,0px)+4.5rem)] z-50 flex size-14 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-primary/30 transition-transform hover:scale-105"
        >
            <svg
                class="size-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                />
            </svg>
            <span
                v-if="unreadMessagesCount > 0"
                class="absolute top-1 right-1.5 flex size-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm"
            >
                {{ unreadMessagesCount > 99 ? '99+' : unreadMessagesCount }}
            </span>
            <span
                v-if="presenceConnected"
                class="absolute right-0 bottom-0 flex size-3.5 items-center justify-center"
            >
                <span
                    class="absolute inline-flex size-full animate-ping rounded-full bg-green-400 opacity-75"
                ></span>
                <span
                    class="relative inline-flex size-3 rounded-full border-2 border-white bg-green-500"
                ></span>
            </span>
        </Link>
        <button
            v-else-if="props.showChatButton"
            type="button"
            @click="chatLoginModalOpen = true"
            class="fixed right-[calc(var(--inset-right,0px)+1rem)] bottom-[calc(var(--inset-bottom,0px)+4.5rem)] z-50 flex size-14 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-primary/30 transition-transform hover:scale-105"
        >
            <svg
                class="size-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                />
            </svg>
        </button>

        <MobileBottomNav
            v-if="props.showBottomBar"
            :active="props.activeNav"
            @login-required="loginModalOpen = true"
        />

        <LocationSelectionModal
            v-if="showLocationModal"
            @close="manualLocationModal = false"
        />

        <LoginModal
            v-if="loginModalOpen"
            @close="loginModalOpen = false"
        />

        <LoginModal
            v-if="chatLoginModalOpen"
            redirect-to="/conversations"
            @close="chatLoginModalOpen = false"
        />

        <SearchModal
            v-if="searchModalOpen"
            @close="searchModalOpen = false"
        />

        <!-- Connection Error Overlay -->
        <ConnectionError
            v-if="showConnectionError"
            :message="errorMessage ?? undefined"
            @retry="retry"
        />
    </div>
</template>

<style scoped>
:global(.mobile-layout-content .sticky) {
    top: var(--mobile-topbar-height) !important;
    z-index: 40;
}

:global(.mobile-top-bar) {
    z-index: 60;
}
</style>
