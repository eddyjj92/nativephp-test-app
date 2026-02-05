<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import type { AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
const location = computed(
    () => page.props.location as { province: any; municipality: any } | null,
);

const provinceName = computed(() => {
    return location.value?.province?.name || 'Ubicación';
});

defineEmits(['open-location']);
</script>

<template>
    <header
        class="mobile-top-bar fixed top-0 right-0 left-0 z-50 bg-slate-50 pt-[calc(var(--inset-top,0px)+5px)] pr-[var(--inset-right,0px)] pl-[var(--inset-left,0px)] shadow-sm dark:bg-slate-900"
    >
        <div class="mb-2 flex items-center justify-between px-4">
            <div class="flex items-center">
                <AppLogoIcon class="h-8 w-auto" />
            </div>

            <div class="flex items-center gap-2">
                <button
                    class="flex items-center gap-1 rounded-full bg-white px-3 py-1.5 shadow-sm transition-colors hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700"
                    @click="$emit('open-location')"
                >
                    <svg
                        class="size-5 text-primary"
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
                    <span
                        class="max-w-[80px] truncate text-xs font-bold text-slate-700 dark:text-slate-200"
                    >
                        {{ provinceName }}
                    </span>
                </button>
            </div>
        </div>

        <div class="px-4 pb-2">
            <div
                class="flex h-12 w-full items-center rounded-xl border border-gray-100 bg-white shadow-sm dark:border-white/5 dark:bg-slate-800/50"
            >
                <div class="pl-4 text-gray-400">
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
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </div>
                <input
                    type="text"
                    placeholder="Buscar productos..."
                    class="flex-1 border-none bg-transparent text-sm font-medium placeholder-gray-400 focus:ring-0"
                />
                <div class="pr-4 text-primary">
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
                            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
                        />
                    </svg>
                </div>
            </div>
        </div>
    </header>
</template>
