<script setup lang="ts"></script>

<template>
    <header class="sticky top-0 z-50 bg-slate-50 pb-2 pt-4 dark:bg-slate-900">
        <div class="mb-2 flex items-center justify-between px-4">
            <div class="flex items-center gap-2">
                <div class="rounded-lg bg-blue-600 p-1">
                    <svg
                        class="size-6 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold tracking-tight">Compay Market</h1>
            </div>
            <button
                class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                aria-label="Notifications"
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
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                    />
                </svg>
            </button>
        </div>

        <div class="px-4 py-2">
            <div
                class="flex h-12 w-full items-center rounded-xl border border-gray-100 bg-white shadow-sm dark:border-white/5 dark:bg-slate-800/50"
            >
                <div class="pl-4 text-gray-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </div>
                <input
                    type="text"
                    placeholder="Search for products..."
                    class="flex-1 border-none bg-transparent text-sm font-medium placeholder-gray-400 focus:ring-0"
                />
                <div class="pr-4 text-blue-600">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
                        />
                    </svg>
                </div>
            </div>
        </div>
    </header>
</template>
