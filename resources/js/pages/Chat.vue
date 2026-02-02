<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

type Message = {
    id: number;
    text: string;
    isSender: boolean;
    time: string;
};

// Mock user data based on "id" (in a real app, this comes from props/db)
const user = ref({
    name: 'Support Team',
    avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDsLJNLbDaRdlWrk30bLVXGz2zMdwABe3pWlNpNb5MV0CKXj6qLqO4WCWYwBrNCJwAF2rAqY0s_4yJ6HZ8E9hJJQ0aYQm_Xq7eJpCwQY7JOmA',
    status: 'Online'
});

const messages = ref<Message[]>([
    { id: 1, text: 'Hi Alex! How can we help you today?', isSender: false, time: '10:00 AM' },
    { id: 2, text: 'I have a question about my order #1234.', isSender: true, time: '10:02 AM' },
    { id: 3, text: 'Sure, let me check that for you. One moment please.', isSender: false, time: '10:03 AM' },
    { id: 4, text: 'Great, thanks!', isSender: true, time: '10:03 AM' },
    { id: 5, text: 'Your order #1234 has been shipped! You should receive it by tomorrow.', isSender: false, time: '10:05 AM' },
]);

const newMessage = ref('');
const messagesContainer = ref<HTMLElement | null>(null);

const sendMessage = () => {
    if (!newMessage.value.trim()) return;
    
    messages.value.push({
        id: Date.now(),
        text: newMessage.value,
        isSender: true,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    });
    
    newMessage.value = '';
    scrollToBottom();
};

const scrollToBottom = () => {
    setTimeout(() => {
        if (messagesContainer.value) {
            window.scrollTo(0, document.body.scrollHeight);
        }
    }, 100);
};

onMounted(() => {
    scrollToBottom();
});
</script>

<template>
    <Head :title="user.name" />

    <!-- Disable default top bar to show custom chat header -->
    <MobileLayout active-nav="home" :show-top-bar="false" :show-chat-button="false" :show-bottom-bar="false">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- Chat Header -->
            <header
                class="sticky top-0 z-50 border-b border-gray-100 bg-slate-50 px-4 pb-3 pt-4 dark:border-white/5 dark:bg-slate-900"
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
                                    class="size-10 rounded-full bg-slate-200 bg-cover bg-center"
                                    :style="{ backgroundImage: `url('${user.avatar}')` }"
                                ></div>
                                <div class="absolute bottom-0 right-0 size-2.5 rounded-full border-2 border-white bg-green-500 dark:border-slate-900"></div>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-base font-bold leading-tight">{{ user.name }}</h1>
                                <span class="text-xs font-medium text-green-600 dark:text-green-400">{{ user.status }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <button class="rounded-full p-2 text-slate-900 transition-colors hover:bg-black/5 dark:text-white dark:hover:bg-white/10">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 12.284 3 6V5z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Messages Area -->
            <main class="flex-1 px-4 pb-24 pt-4" ref="messagesContainer">
                <div class="flex flex-col gap-4">
                    <div
                        v-for="msg in messages"
                        :key="msg.id"
                        class="flex w-full"
                        :class="msg.isSender ? 'justify-end' : 'justify-start'"
                    >
                        <div
                            class="max-w-[75%] rounded-2xl px-4 py-3 shadow-sm"
                            :class="[
                                msg.isSender
                                    ? 'bg-blue-600 text-white rounded-br-none'
                                    : 'bg-white text-slate-900 dark:bg-slate-800/50 dark:text-white rounded-bl-none'
                            ]"
                        >
                            <p class="text-sm leading-relaxed">{{ msg.text }}</p>
                            <p
                                class="mt-1 text-[10px]"
                                :class="msg.isSender ? 'text-blue-100' : 'text-slate-400'"
                            >
                                {{ msg.time }}
                            </p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Input Area -->
            <div
                class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-100 bg-white px-4 pb-8 pt-2 dark:border-white/5 dark:bg-slate-900"
            >
                <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                    <button
                        type="button"
                        class="flex size-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition-colors hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700"
                    >
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                    
                    <div class="flex-1 rounded-full bg-slate-100 px-4 py-3 dark:bg-slate-800">
                        <input
                            v-model="newMessage"
                            type="text"
                            placeholder="Type a message..."
                            class="w-full border-none bg-transparent p-0 text-sm placeholder-slate-400 focus:ring-0 dark:text-white"
                        />
                    </div>

                    <button
                        type="submit"
                        class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white transition-colors hover:bg-blue-700 disabled:opacity-50"
                        :disabled="!newMessage.trim()"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </MobileLayout>
</template>
