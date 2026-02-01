<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

type Conversation = {
    id: number;
    name: string;
    avatar: string;
    lastMessage: string;
    time: string;
    unreadCount: number;
    isOnline: boolean;
};

const conversations = ref<Conversation[]>([
    {
        id: 1,
        name: 'Support Team',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDsLJNLbDaRdlWrk30bLVXGz2zMdwABe3pWlNpNb5MV0CKXj6qLqO4WCWYwBrNCJwAF2rAqY0s_4yJ6HZ8E9hJJQ0aYQm_Xq7eJpCwQY7JOmA',
        lastMessage: 'Your order #1234 has been shipped!',
        time: '2m ago',
        unreadCount: 2,
        isOnline: true,
    },
    {
        id: 2,
        name: 'Alex Johnson',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBkdQgUIXRO2xmxQisdRa-xXNF5Xf60N8b147d5kkuAsHkR2B8sxIweopIOP4TQ-ZyafkU2CfP4ZqxpBxgSIVrL-hQXygWvNhurSpaxY1W29qELyh_NYKTClebstrSp-ZInNYMiAINV53-BKWqWJbK6YCaL1sphk566QGSEySsVrkdma9yrFh8Edcf_lzXyn431pIhFDUY8Wo7QHDbYZIIzeyKD0wibiAsBRfHXaOz5MwXWmY14QGmWO0YVxrtlKxPiUre2bKtYgks',
        lastMessage: 'Thanks for the review!',
        time: '1h ago',
        unreadCount: 0,
        isOnline: false,
    },
    {
        id: 3,
        name: 'Sarah Williams',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCx8jrIDsQet2gsCPjmllYeBU_c-nkpb_O0bu7E910IcOGwqTbGldHyC6AhFT2sHkACQ4x4cR2T9Mh1IInp8e5aBXI7jn6E0lyyHDGwVIHStqpDWJ9OOicbmpDhOEMZh_uxumz-MzCTqI2RFfHkOzH-UGFhnEpyKptYIrKFtQYFsTO5hFeXPhU3A3irp94FpYbrwyIpg7u9PKwp_yw7f-51g1cRVM88qLCJnpMb9-0zX67a9_dQKvGbsq9BEdT6CQ_OJ9QjGyd-3GQ',
        lastMessage: 'Is this item still available in red?',
        time: 'Yesterday',
        unreadCount: 1,
        isOnline: true,
    },
    {
        id: 4,
        name: 'Michael Brown',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO4f9Y-jqkXBYxkKrIgQT2ozhzYGez20ZAncUOIIpa4vOjsTFuIiyrEwNPGutfh0j83ghqFinrhrmQnGRA5oZbFP9mjrC5ezTwXFcmjNukRjGMoFFf0So2n4RNdKKj1VOTroo-omFNbzEizT9CJpdt3AUErlwQ5fYyeELD2bROrjaXt4lKZhn0rGkXxwVkMXEN3GgmcWhdYvPDRTX1MY3xGH8ZOte8Kb1Xy19CFnoeQ3FRIWqQrIqv4iBCVKkQcW9kpTHQ6jXoEMw',
        lastMessage: 'Can you deliver by Friday?',
        time: 'Oct 24',
        unreadCount: 0,
        isOnline: false,
    },
]);
</script>

<template>
    <Head title="Messages" />

    <MobileLayout active-nav="home" :show-chat-button="false" :show-bottom-bar="true">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- Header (Standardized) -->
            <header
                class="sticky top-0 z-50 border-b border-gray-100 bg-slate-50 pb-3 pt-4 dark:border-white/5 dark:bg-slate-900"
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
                        <h1 class="text-lg font-bold">Messages</h1>
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
            <main class="flex-1 px-4 pt-2 pb-24">
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
                                class="size-12 rounded-full bg-slate-200 bg-cover bg-center"
                                :style="{ backgroundImage: `url('${chat.avatar}')` }"
                            ></div>
                            <div
                                v-if="chat.isOnline"
                                class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-white bg-green-500 dark:border-slate-800"
                            ></div>
                        </div>

                        <!-- Info -->
                        <div class="flex min-w-0 flex-1 flex-col gap-1">
                            <div class="flex items-center justify-between">
                                <h3 class="truncate text-sm font-bold text-slate-900 dark:text-white">
                                    {{ chat.name }}
                                </h3>
                                <span class="text-xs font-medium text-slate-400">
                                    {{ chat.time }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <p
                                    class="truncate text-sm"
                                    :class="chat.unreadCount > 0 ? 'font-semibold text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400'"
                                >
                                    {{ chat.lastMessage }}
                                </p>
                                <div
                                    v-if="chat.unreadCount > 0"
                                    class="flex size-5 shrink-0 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white"
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
