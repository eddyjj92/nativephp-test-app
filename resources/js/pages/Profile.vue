<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import MobileLayout from '@/layouts/MobileLayout.vue';
</script>

<template>
    <Head title="User Profile - Compay Market">
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        />
    </Head>

    <MobileLayout active-nav="profile">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- TopAppBar -->
            <header class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900">

                <div class="mb-3 flex items-center justify-between px-4">
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
                        <h1 class="text-lg font-bold">Profile</h1>
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
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- ProfileHeader -->
                <div class="flex px-4 pb-4 @container">
                    <div class="flex w-full flex-col gap-4 items-center">
                        <div class="flex gap-4 flex-col items-center">
                            <div class="relative">
                                <div
                                    class="bg-center bg-no-repeat aspect-square bg-cover rounded-full min-h-32 w-32 border-4 border-primary/10"
                                    data-alt="User profile picture showing a smiling person"
                                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCG66UxMbuzQ0pc9itREWkbr2giJqFBq93cjLIAJdzah3OH00cVgDnLoCwy4Z2BUsUzAX2UHCHzArim80KszwnoaBghI9l3EoAsjF77MD9uzz80g6Tu7OEXhZhKATNgnkYKVHNN8nKn1JtE5kpsl2ZSgW4tA6G6PcwsO0jp_hv61EYirL9AChO2wh6H2h8VedkRdhrwKXYfb33uf8owON8gYZ0_52XFrhWaCSK7FXGNLBBqm0-IzClZeQuTmnob8gUh_fJTF4IVkR4');"
                                ></div>
                                <div class="absolute bottom-1 right-1 bg-primary text-white p-1.5 rounded-full border-2 border-white dark:border-slate-900">
                                    <span class="material-symbols-outlined text-[16px] block">edit</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-center justify-center">
                                <h2 class="text-slate-900 dark:text-white text-[22px] font-bold leading-tight tracking-[-0.015em] text-center">
                                    Alex Thompson
                                </h2>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                        Gold Member
                                    </span>
                                </div>
                                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">
                                    Member since 2022
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TextGrid (Quick Actions) -->
                <div class="grid grid-cols-2 gap-3 p-4">
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-800/50 p-4 flex-col cursor-pointer transition-all active:scale-95 shadow-sm"
                    >
                        <div class="text-primary" data-icon="ShoppingBag">
                            <span class="material-symbols-outlined">shopping_bag</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-slate-900 dark:text-white text-base font-bold leading-tight">
                                My Orders
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-normal leading-normal">
                                1 Active Order
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-800/50 p-4 flex-col cursor-pointer transition-all active:scale-95 shadow-sm"
                    >
                        <div class="text-primary" data-icon="MapPin">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-slate-900 dark:text-white text-base font-bold leading-tight">
                                Shipping
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-normal leading-normal">
                                2 Addresses
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-800/50 p-4 flex-col cursor-pointer transition-all active:scale-95 shadow-sm"
                    >
                        <div class="text-primary" data-icon="CreditCard">
                            <span class="material-symbols-outlined">credit_card</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-slate-900 dark:text-white text-base font-bold leading-tight">
                                Payments
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-normal leading-normal">
                                Visa •••• 4242
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-800/50 p-4 flex-col cursor-pointer transition-all active:scale-95 shadow-sm"
                    >
                        <div class="text-primary" data-icon="Ticket">
                            <span class="material-symbols-outlined">confirmation_number</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-slate-900 dark:text-white text-base font-bold leading-tight">
                                Coupons
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-normal leading-normal">
                                4 Available
                            </p>
                        </div>
                    </div>
                </div>
                <!-- SectionHeader -->
                <div class="mt-2">
                    <h3 class="text-slate-900 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">
                        Account Settings
                    </h3>
                </div>
                <!-- ListItems -->
                <div class="flex flex-col">
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-slate-800/30 px-4 min-h-[64px] justify-between cursor-pointer active:bg-slate-100 dark:active:bg-slate-800 border-b border-slate-50 dark:border-white/5"
                    >
                        <div class="flex items-center gap-4">
                            <div class="text-primary flex items-center justify-center rounded-xl bg-primary/10 shrink-0 size-10">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <p class="text-slate-900 dark:text-white text-base font-semibold leading-normal flex-1 truncate">
                                Personal Information
                            </p>
                        </div>
                        <div class="shrink-0">
                            <span class="material-symbols-outlined text-slate-400">chevron_right</span>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-slate-800/30 px-4 min-h-[64px] justify-between cursor-pointer active:bg-slate-100 dark:active:bg-slate-800 border-b border-slate-50 dark:border-white/5"
                    >
                        <div class="flex items-center gap-4">
                            <div class="text-primary flex items-center justify-center rounded-xl bg-primary/10 shrink-0 size-10">
                                <span class="material-symbols-outlined">help_center</span>
                            </div>
                            <p class="text-slate-900 dark:text-white text-base font-semibold leading-normal flex-1 truncate">
                                Help Center
                            </p>
                        </div>
                        <div class="shrink-0">
                            <span class="material-symbols-outlined text-slate-400">chevron_right</span>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-slate-800/30 px-4 min-h-[64px] justify-between cursor-pointer active:bg-slate-100 dark:active:bg-slate-800"
                    >
                        <div class="flex items-center gap-4">
                            <div class="text-red-500 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-950/30 shrink-0 size-10">
                                <span class="material-symbols-outlined">logout</span>
                            </div>
                            <p class="text-red-500 text-base font-semibold leading-normal flex-1 truncate">
                                Logout
                            </p>
                        </div>
                        <div class="shrink-0">
                            <span class="material-symbols-outlined text-slate-400">chevron_right</span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </MobileLayout>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
