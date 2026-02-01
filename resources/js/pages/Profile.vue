<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import MobileLayout from '@/layouts/MobileLayout.vue';
</script>

<template>
    <Head title="User Profile - Compay Market">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        />
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        />
    </Head>

    <MobileLayout active-nav="profile">
        <div
            class="profile-screen nativephp-safe-area flex min-h-screen flex-col bg-background-light text-[#111418] antialiased dark:bg-background-dark dark:text-white"
        >
            <!-- TopAppBar -->
            <header
                class="sticky top-0 z-50 flex items-center justify-between bg-white px-4 py-3 dark:bg-background-dark border-b border-gray-100 dark:border-white/5"
            >
                <div class="flex items-center gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full transition-colors hover:bg-black/5 dark:hover:bg-white/10 cursor-pointer">
                        <svg
                            class="size-6 text-[#111418] dark:text-white"
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
                    </div>
                    <h1 class="text-[#111418] dark:text-white text-lg font-bold leading-tight tracking-[-0.015em]">
                        Profile
                    </h1>
                </div>
                <div class="flex w-12 items-center justify-end">
                    <button
                        class="flex cursor-pointer items-center justify-center overflow-hidden rounded-xl size-10 bg-transparent text-[#111418] dark:text-white transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                    >
                        <span class="material-symbols-outlined">settings</span>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 pb-32 pt-4">
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
                                <div class="absolute bottom-1 right-1 bg-primary text-white p-1.5 rounded-full border-2 border-white dark:border-background-dark">
                                    <span class="material-symbols-outlined text-[16px] block">edit</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-center justify-center">
                                <h2 class="text-[#111418] dark:text-white text-[22px] font-bold leading-tight tracking-[-0.015em] text-center">
                                    Alex Thompson
                                </h2>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                        Gold Member
                                    </span>
                                </div>
                                <p class="text-[#617589] dark:text-gray-400 text-sm font-medium mt-1">
                                    Member since 2022
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TextGrid (Quick Actions) -->
                <div class="grid grid-cols-2 gap-3 p-4">
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-[#dbe0e6] dark:border-gray-800 bg-white dark:bg-gray-900/50 p-4 flex-col cursor-pointer transition-all active:scale-95"
                    >
                        <div class="text-primary" data-icon="ShoppingBag">
                            <span class="material-symbols-outlined">shopping_bag</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-[#111418] dark:text-white text-base font-bold leading-tight">
                                My Orders
                            </h2>
                            <p class="text-[#617589] dark:text-gray-400 text-xs font-normal leading-normal">
                                1 Active Order
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-[#dbe0e6] dark:border-gray-800 bg-white dark:bg-gray-900/50 p-4 flex-col cursor-pointer transition-all active:scale-95"
                    >
                        <div class="text-primary" data-icon="MapPin">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-[#111418] dark:text-white text-base font-bold leading-tight">
                                Shipping
                            </h2>
                            <p class="text-[#617589] dark:text-gray-400 text-xs font-normal leading-normal">
                                2 Addresses
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-[#dbe0e6] dark:border-gray-800 bg-white dark:bg-gray-900/50 p-4 flex-col cursor-pointer transition-all active:scale-95"
                    >
                        <div class="text-primary" data-icon="CreditCard">
                            <span class="material-symbols-outlined">credit_card</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-[#111418] dark:text-white text-base font-bold leading-tight">
                                Payments
                            </h2>
                            <p class="text-[#617589] dark:text-gray-400 text-xs font-normal leading-normal">
                                Visa •••• 4242
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-[#dbe0e6] dark:border-gray-800 bg-white dark:bg-gray-900/50 p-4 flex-col cursor-pointer transition-all active:scale-95"
                    >
                        <div class="text-primary" data-icon="Ticket">
                            <span class="material-symbols-outlined">confirmation_number</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-[#111418] dark:text-white text-base font-bold leading-tight">
                                Coupons
                            </h2>
                            <p class="text-[#617589] dark:text-gray-400 text-xs font-normal leading-normal">
                                4 Available
                            </p>
                        </div>
                    </div>
                </div>
                <!-- SectionHeader -->
                <div class="mt-2">
                    <h3 class="text-[#111418] dark:text-white text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">
                        Account Settings
                    </h3>
                </div>
                <!-- ListItems -->
                <div class="flex flex-col">
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-transparent px-4 min-h-[64px] justify-between cursor-pointer active:bg-gray-50 dark:active:bg-gray-800"
                    >
                        <div class="flex items-center gap-4">
                            <div class="text-primary flex items-center justify-center rounded-xl bg-primary/10 shrink-0 size-10">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <p class="text-[#111418] dark:text-white text-base font-semibold leading-normal flex-1 truncate">
                                Personal Information
                            </p>
                        </div>
                        <div class="shrink-0">
                            <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-transparent px-4 min-h-[64px] justify-between cursor-pointer active:bg-gray-50 dark:active:bg-gray-800"
                    >
                        <div class="flex items-center gap-4">
                            <div class="text-primary flex items-center justify-center rounded-xl bg-primary/10 shrink-0 size-10">
                                <span class="material-symbols-outlined">help_center</span>
                            </div>
                            <p class="text-[#111418] dark:text-white text-base font-semibold leading-normal flex-1 truncate">
                                Help Center
                            </p>
                        </div>
                        <div class="shrink-0">
                            <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-transparent px-4 min-h-[64px] justify-between cursor-pointer active:bg-gray-50 dark:active:bg-gray-800"
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
                            <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </MobileLayout>
</template>

<style scoped>
.profile-screen {
    min-height: max(884px, 100dvh);
    font-family:
        'Manrope',
        'Instrument Sans',
        ui-sans-serif,
        system-ui,
        sans-serif,
        'Apple Color Emoji',
        'Segoe UI Emoji',
        'Segoe UI Symbol',
        'Noto Color Emoji';
}

.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.material-symbols-outlined.active-nav {
    font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

:global(.bg-background-light) {
    background-color: #f6f7f8;
}

:global(.dark .bg-background-light),
:global(.bg-background-dark) {
    background-color: #101922;
}

:global(.text-primary) {
    color: #137fec;
}

:global(.bg-primary) {
    background-color: #137fec;
}

:global(.bg-primary\/10) {
    background-color: rgba(19, 127, 236, 0.1);
}

:global(.border-primary\/10) {
    border-color: rgba(19, 127, 236, 0.1);
}

:global(.dark .dark\:bg-background-dark) {
    background-color: #101922;
}

:global(.dark .dark\:bg-background-dark\/80) {
    background-color: rgba(16, 25, 34, 0.8);
}

:global(.dark .dark\:border-background-dark) {
    border-color: #101922;
}
</style>
