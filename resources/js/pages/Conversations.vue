<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';
import { useOnlineUsers } from '@/composables/useEcho';

type ConversationUser = {
    id: number;
    name: string;
    avatar: string | null;
};

type ApiConversation = {
    id: number;
    user_one_id: number;
    user_two_id: number | null;
    type: string;
    last_message_at: string | null;
    unread_count: number;
    user_one: ConversationUser | null;
    user_two: ConversationUser | null;
    last_message: {
        content: string | null;
    } | null;
};

type ViewConversation = {
    id: number;
    name: string;
    avatar: string | null;
    initials: string;
    lastMessage: string;
    time: string;
    unreadCount: number;
    otherUserId: number;
};

const props = withDefaults(
    defineProps<{
        conversations?: ApiConversation[];
        isStaff?: boolean;
        error?: string;
    }>(),
    {
        conversations: () => [],
        isStaff: false,
        error: undefined,
    },
);

const page = usePage();

const onlineUsers = useOnlineUsers();

const authUserId = computed<number | null>(() => {
    const userId = (page.props.auth as { user?: { id?: number } } | undefined)
        ?.user?.id;

    return typeof userId === 'number' ? userId : null;
});

function formatTime(dateTime: string | null): string {
    if (!dateTime) {
        return '';
    }

    const date = new Date(dateTime);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    const now = new Date();
    const isToday = date.toDateString() === now.toDateString();

    if (isToday) {
        return date.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
    });
}

function getInitials(name: string): string {
    const words = name.trim().split(' ').filter(Boolean);

    return (
        words
            .slice(0, 2)
            .map((word) => word[0]?.toUpperCase() ?? '')
            .join('') || '?'
    );
}

function getConversationUser(
    conversation: ApiConversation,
): ConversationUser | null {
    if (conversation.type === 'support') {
        if (props.isStaff) {
            return conversation.user_one;
        }

        return {
            id: 0,
            name: 'Soporte',
            avatar: null,
        };
    }

    if (authUserId.value === conversation.user_one_id) {
        return conversation.user_two;
    }

    if (authUserId.value === conversation.user_two_id) {
        return conversation.user_one;
    }

    return conversation.user_one ?? conversation.user_two;
}

const conversations = computed<ViewConversation[]>(() => {
    return props.conversations.map((conversation) => {
        const displayUser = getConversationUser(conversation);
        const displayName = displayUser?.name ?? 'Conversación';

        return {
            id: conversation.id,
            name: displayName,
            avatar: displayUser?.avatar ?? null,
            initials: getInitials(displayName),
            lastMessage: conversation.last_message?.content ?? 'Sin mensajes',
            time: formatTime(conversation.last_message_at),
            unreadCount: conversation.unread_count ?? 0,
            otherUserId: displayUser?.id ?? 0,
        };
    });
});
</script>

<template>
    <Head title="Messages" />

    <MobileLayout
        active-nav="home"
        :show-chat-button="false"
        :show-bottom-bar="true"
    >
        <div
            class="flex flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- Header (Standardized) -->
            <header
                class="fixed top-[calc(var(--inset-top,0px)+100px)] right-0 left-0 z-40 bg-slate-50 pt-3 pb-2 dark:bg-slate-900"
            >
                <div class="flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/"
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <svg
                                class="size-6 text-slate-900 dark:text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </Link>
                        <h1 class="text-lg font-bold">Mis Mensajes</h1>
                    </div>
                    <div class="flex items-center">
                        <button
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <svg
                                class="size-6 text-slate-900 dark:text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 px-4 pt-[calc(var(--inset-top,0px)+60px)] pb-4">
                <div
                    v-if="props.error"
                    class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/30"
                >
                    <p class="text-sm text-red-600 dark:text-red-400">
                        {{ props.error }}
                    </p>
                </div>

                <div
                    v-if="conversations.length === 0"
                    class="mb-4 rounded-2xl bg-white p-6 text-center text-sm text-slate-500 shadow-sm dark:bg-slate-800/50 dark:text-slate-400"
                >
                    No tienes conversaciones por el momento.
                </div>

                <div class="flex flex-col gap-2">
                    <Link
                        v-for="chat in conversations"
                        :key="chat.id"
                        :href="`/conversations/${chat.id}`"
                        class="flex items-center gap-4 rounded-2xl bg-white p-3 shadow-sm transition-colors active:scale-[0.99] dark:bg-slate-800/50"
                    >
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            <div
                                class="flex size-12 items-center justify-center rounded-full bg-slate-200 bg-cover bg-center text-sm font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-300"
                                :style="
                                    chat.avatar
                                        ? {
                                              backgroundImage: `url('${chat.avatar}')`,
                                          }
                                        : undefined
                                "
                            >
                                <span v-if="!chat.avatar">{{
                                    chat.initials
                                }}</span>
                            </div>
                            <span
                                v-if="onlineUsers.has(chat.otherUserId)"
                                class="absolute right-0 bottom-0 flex size-3 items-center justify-center"
                            >
                                <span
                                    class="absolute inline-flex size-full animate-ping rounded-full bg-green-400 opacity-75"
                                ></span>
                                <span
                                    class="relative inline-flex size-2.5 rounded-full border-2 border-white bg-green-500 dark:border-slate-800"
                                ></span>
                            </span>
                        </div>

                        <!-- Info -->
                        <div class="flex min-w-0 flex-1 flex-col gap-1">
                            <div class="flex items-center justify-between">
                                <h3
                                    class="truncate text-sm font-bold text-slate-900 dark:text-white"
                                >
                                    {{ chat.name }}
                                </h3>
                                <span
                                    class="text-xs font-medium text-slate-400"
                                >
                                    {{ chat.time }}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between gap-2"
                            >
                                <p
                                    class="truncate text-sm"
                                    :class="
                                        chat.unreadCount > 0
                                            ? 'font-semibold text-slate-900 dark:text-white'
                                            : 'text-slate-500 dark:text-slate-400'
                                    "
                                >
                                    {{ chat.lastMessage }}
                                </p>
                                <div
                                    v-if="chat.unreadCount > 0"
                                    class="flex size-5 shrink-0 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white"
                                >
                                    {{ chat.unreadCount }}
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </main>
        </div>
    </MobileLayout>
</template>
