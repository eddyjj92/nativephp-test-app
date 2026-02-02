<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const location = computed(() => page.props.location as { province: any; municipality: any } | null);

const provinceName = computed(() => {
    return location.value?.province?.name || 'Location';
});

const emit = defineEmits(['open-location']);
</script>

<template>
    <header class="mobile-top-bar sticky top-0 z-50 bg-slate-50 pb-2 pt-4 dark:bg-slate-900">
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
                class="flex items-center gap-1 rounded-full bg-white px-3 py-1.5 shadow-sm transition-colors hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700"
                @click="$emit('open-location')"
            >
                <svg
                    class="size-5 text-blue-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                </svg>
                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 max-w-[80px] truncate">
                    {{ provinceName }}
                </span>
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
