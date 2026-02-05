<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ChevronLeft, Scale } from 'lucide-vue-next';
import { computed } from 'vue';
import type { AppPageProps } from '@/types';

import MobileLayout from '@/layouts/MobileLayout.vue';

const page = usePage<AppPageProps>();
const settings = computed(() => page.props.settings);
</script>

<template>
    <Head title="Aviso Legal - Compay Market" />

    <MobileLayout active-nav="profile" :show-chat-button="false">
        <div
            class="flex flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- TopAppBar -->
            <header class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/help"
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <ChevronLeft class="size-6 text-slate-900 dark:text-white" />
                        </Link>
                        <h1 class="text-lg font-bold">Aviso Legal</h1>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- Header Section -->
                <div class="flex flex-col items-center pb-6 text-center">
                    <div class="flex size-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                        <Scale class="size-8 text-slate-600 dark:text-slate-400" />
                    </div>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        Información legal y regulatoria
                    </p>
                </div>

                <!-- Content -->
                <div
                    class="rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5"
                >
                    <div
                        v-if="settings.legal_notice"
                        class="prose prose-sm prose-slate max-w-none dark:prose-invert prose-headings:font-bold prose-p:text-slate-600 dark:prose-p:text-slate-400 prose-a:text-primary"
                        v-html="settings.legal_notice"
                    />
                    <div v-else class="py-8 text-center">
                        <Scale class="mx-auto size-12 text-slate-300 dark:text-slate-600" />
                        <p class="mt-3 text-slate-500 dark:text-slate-400">
                            El aviso legal no está disponible en este momento.
                        </p>
                    </div>
                </div>

                <!-- Company Info -->
                <div class="mt-6 rounded-2xl bg-slate-100 p-4 dark:bg-slate-800">
                    <h4 class="font-semibold text-slate-800 dark:text-slate-200">
                        {{ settings.site_name }}
                    </h4>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        {{ settings.address }}
                    </p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        {{ settings.email }}
                    </p>
                </div>
            </main>
        </div>
    </MobileLayout>
</template>

<style scoped>
:deep(.prose) {
    font-size: 0.875rem;
    line-height: 1.7;
}

:deep(.prose h1),
:deep(.prose h2),
:deep(.prose h3) {
    margin-top: 1.5em;
    margin-bottom: 0.5em;
}

:deep(.prose p) {
    margin-bottom: 1em;
}

:deep(.prose ul),
:deep(.prose ol) {
    padding-left: 1.5em;
    margin-bottom: 1em;
}
</style>
