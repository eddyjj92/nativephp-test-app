<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
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
    created_at: string | null;
    updated_at: string | null;
    user_one?: ConversationUser | null;
    user_two?: ConversationUser | null;
};

type ApiMessage = {
    id: number;
    conversation_id: number;
    sender_id: number;
    content: string;
    type: string;
    file_path: string | null;
    file_name: string | null;
    read_at: string | null;
    created_at: string;
    updated_at: string;
    sender: {
        id: number;
        name: string;
        avatar: string | null;
    };
};

type PaginatedMessages = {
    current_page: number;
    data: ApiMessage[];
    last_page: number;
    next_page_url: string | null;
    per_page: number;
    total: number;
};

type ViewMessage = {
    id: number;
    text: string;
    isSender: boolean;
    time: string;
};

const props = withDefaults(
    defineProps<{
        conversation?: ApiConversation | null;
        messages?: PaginatedMessages | null;
        error?: string;
    }>(),
    {
        conversation: null,
        messages: null,
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

const otherUser = computed<ConversationUser>(() => {
    if (!props.conversation) {
        return { id: 0, name: 'Chat', avatar: null };
    }

    if (props.conversation.type === 'support') {
        return { id: 0, name: 'Soporte', avatar: null };
    }

    if (authUserId.value === props.conversation.user_one_id) {
        return (
            props.conversation.user_two ?? {
                id: 0,
                name: 'Usuario',
                avatar: null,
            }
        );
    }

    return (
        props.conversation.user_one ?? {
            id: 0,
            name: 'Usuario',
            avatar: null,
        }
    );
});

const isOtherUserOnline = computed(
    () => otherUser.value.id > 0 && onlineUsers.value.has(otherUser.value.id),
);

function formatTime(dateTime: string): string {
    const date = new Date(dateTime);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
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

function mapMessage(msg: ApiMessage): ViewMessage {
    return {
        id: msg.id,
        text: msg.content,
        isSender: msg.sender_id === authUserId.value,
        time: formatTime(msg.created_at),
    };
}

const displayMessages = ref<ViewMessage[]>([]);
const messagesContainer = ref<HTMLElement | null>(null);
const scrollSentinel = ref<HTMLElement | null>(null);
const newMessage = ref('');
const sending = ref(false);
const loadingMore = ref(false);
const nextPage = ref<number | null>(null);
let observer: IntersectionObserver | null = null;

function scrollToBottom(): void {
    nextTick(() => {
        setTimeout(() => {
            const container = messagesContainer.value;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 50);
    });
}

async function loadOlderMessages(): Promise<void> {
    if (loadingMore.value || !nextPage.value || !props.conversation) {
        return;
    }

    const container = messagesContainer.value;
    if (!container) {
        return;
    }

    loadingMore.value = true;

    const previousHeight = container.scrollHeight;

    try {
        const { data } = await axios.get<PaginatedMessages>(
            `/conversations/${props.conversation.id}/messages`,
            { params: { page: nextPage.value } },
        );

        const olderMessages = (data.data ?? []).map(mapMessage);
        displayMessages.value.unshift(...olderMessages);

        nextPage.value =
            data.current_page < data.last_page
                ? data.current_page + 1
                : null;

        nextTick(() => {
            container.scrollTop = container.scrollHeight - previousHeight;
        });
    } catch {
        // Silently fail, user can scroll up again to retry
    } finally {
        loadingMore.value = false;
    }
}

function setupScrollObserver(): void {
    if (!scrollSentinel.value || !messagesContainer.value) {
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            if (entries[0]?.isIntersecting) {
                loadOlderMessages();
            }
        },
        { root: messagesContainer.value, threshold: 0.1 },
    );

    observer.observe(scrollSentinel.value);
}

async function sendMessage(): Promise<void> {
    if (!newMessage.value.trim() || sending.value || !props.conversation) {
        return;
    }

    const text = newMessage.value.trim();
    newMessage.value = '';
    sending.value = true;

    const tempMessage: ViewMessage = {
        id: Date.now(),
        text,
        isSender: true,
        time: new Date().toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
        }),
    };

    displayMessages.value.push(tempMessage);
    scrollToBottom();

    try {
        await axios.post(`/conversations/${props.conversation.id}/messages`, {
            receiver_id: otherUser.value.id,
            message: text,
        });
    } catch {
        const index = displayMessages.value.indexOf(tempMessage);
        if (index > -1) {
            displayMessages.value.splice(index, 1);
        }
    } finally {
        sending.value = false;
    }
}

onMounted(() => {
    const paginated = props.messages;

    if (paginated?.data) {
        displayMessages.value = paginated.data.map(mapMessage);
        nextPage.value =
            paginated.current_page < paginated.last_page
                ? paginated.current_page + 1
                : null;
    }

    scrollToBottom();

    nextTick(() => {
        setupScrollObserver();
    });
});

onUnmounted(() => {
    observer?.disconnect();
});
</script>

<template>
    <Head :title="otherUser.name" />

    <MobileLayout
        active-nav="home"
        :show-top-bar="false"
        :show-chat-button="false"
        :show-bottom-bar="false"
    >
        <div
            class="fixed inset-0 z-40 flex flex-col bg-slate-50 pt-[var(--inset-top,0px)] dark:bg-slate-900"
        >
            <!-- Chat Header -->
            <header
                class="shrink-0 border-b border-gray-100 bg-slate-50 px-4 pb-3 pt-4 dark:border-white/5 dark:bg-slate-900"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/conversations"
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

                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div
                                    v-if="otherUser.avatar"
                                    class="size-10 rounded-full bg-slate-200 bg-cover bg-center"
                                    :style="{
                                        backgroundImage: `url('${otherUser.avatar}')`,
                                    }"
                                ></div>
                                <div
                                    v-else
                                    class="flex size-10 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-300"
                                >
                                    {{ getInitials(otherUser.name) }}
                                </div>
                                <span
                                    v-if="isOtherUserOnline"
                                    class="absolute right-0 bottom-0 flex size-3 items-center justify-center"
                                >
                                    <span
                                        class="absolute inline-flex size-full animate-ping rounded-full bg-green-400 opacity-75"
                                    ></span>
                                    <span
                                        class="relative inline-flex size-2.5 rounded-full border-2 border-white bg-green-500 dark:border-slate-900"
                                    ></span>
                                </span>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-base font-bold leading-tight">
                                    {{ otherUser.name }}
                                </h1>
                                <span
                                    v-if="isOtherUserOnline"
                                    class="text-xs font-medium text-green-600 dark:text-green-400"
                                >
                                    En línea
                                </span>
                                <span
                                    v-else
                                    class="text-xs font-medium text-slate-400"
                                >
                                    Desconectado
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Messages Area -->
            <main
                ref="messagesContainer"
                class="flex-1 overflow-y-auto px-4 pb-4 pt-4"
            >
                <!-- Scroll sentinel for infinite scroll (load older messages) -->
                <div ref="scrollSentinel" class="h-1" />

                <!-- Loading indicator -->
                <div
                    v-if="loadingMore"
                    class="flex justify-center py-4"
                >
                    <div
                        class="size-6 animate-spin rounded-full border-2 border-slate-300 border-t-primary"
                    ></div>
                </div>

                <div
                    v-if="props.error"
                    class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/30"
                >
                    <p class="text-sm text-red-600 dark:text-red-400">
                        {{ props.error }}
                    </p>
                </div>

                <div
                    v-if="displayMessages.length === 0 && !props.error"
                    class="py-12 text-center text-sm text-slate-400"
                >
                    No hay mensajes aún. Envía el primero.
                </div>

                <div class="flex flex-col gap-4">
                    <div
                        v-for="msg in displayMessages"
                        :key="msg.id"
                        class="flex w-full"
                        :class="
                            msg.isSender ? 'justify-end' : 'justify-start'
                        "
                    >
                        <div
                            class="max-w-[75%] rounded-2xl px-4 py-3 shadow-sm"
                            :class="[
                                msg.isSender
                                    ? 'rounded-br-none bg-primary text-white'
                                    : 'rounded-bl-none bg-white text-slate-900 dark:bg-slate-800/50 dark:text-white',
                            ]"
                        >
                            <p class="text-sm leading-relaxed">
                                {{ msg.text }}
                            </p>
                            <p
                                class="mt-1 text-[10px]"
                                :class="
                                    msg.isSender
                                        ? 'text-blue-100'
                                        : 'text-slate-400'
                                "
                            >
                                {{ msg.time }}
                            </p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Input Area -->
            <div
                class="shrink-0 border-t border-gray-100 bg-white/90 px-6 pb-[calc(var(--inset-bottom,0px)+0.5rem)] pl-[calc(var(--inset-left,0px)+1.5rem)] pr-[calc(var(--inset-right,0px)+1.5rem)] pt-2 backdrop-blur-md dark:border-white/5 dark:bg-slate-900/95"
            >
                <form
                    class="flex w-full items-center gap-2"
                    @submit.prevent="sendMessage"
                >
                    <button
                        type="button"
                        class="flex size-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition-colors hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700"
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
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>
                    </button>

                    <div
                        class="flex-1 rounded-full bg-slate-100 px-4 py-3 dark:bg-slate-800"
                    >
                        <input
                            v-model="newMessage"
                            type="text"
                            placeholder="Escribe un mensaje..."
                            class="w-full border-none bg-transparent p-0 text-sm placeholder-slate-400 focus:ring-0 dark:text-white"
                        />
                    </div>

                    <button
                        type="submit"
                        class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary text-white transition-colors hover:bg-primary/90 disabled:opacity-50"
                        :disabled="!newMessage.trim() || sending"
                    >
                        <svg
                            class="size-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                            />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </MobileLayout>
</template>
