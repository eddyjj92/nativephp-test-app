<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ChevronLeft, FileText } from 'lucide-vue-next';
import { computed } from 'vue';
import type { AppPageProps } from '@/types';

import MobileLayout from '@/layouts/MobileLayout.vue';

const page = usePage<AppPageProps>();
const settings = computed(() => page.props.settings);
</script>

<template>
    <Head title="Términos y Condiciones - Compay Market" />

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
                        <h1 class="text-lg font-bold">Términos y Condiciones</h1>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- Header Section -->
                <div class="flex flex-col items-center pb-6 text-center">
                    <div class="flex size-16 items-center justify-center rounded-full bg-purple-50 dark:bg-purple-900/20">
                        <FileText class="size-8 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>

                <!-- Content -->
                <div
                    class="rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5"
                >
                    <div
                        v-if="settings.terms_conditions"
                        class="prose prose-sm prose-slate max-w-none dark:prose-invert prose-headings:font-bold prose-p:text-slate-600 dark:prose-p:text-slate-400 prose-a:text-primary"
                        v-html="settings.terms_conditions"
                    />
                    <div v-else class="py-8 text-center">
                        <FileText class="mx-auto size-12 text-slate-300 dark:text-slate-600" />
                        <p class="mt-3 text-slate-500 dark:text-slate-400">
                            Los términos y condiciones no están disponibles en este momento.
                        </p>
                    </div>
                </div>

                <!-- Last Updated -->
                <p class="mt-4 text-center text-xs text-slate-400 dark:text-slate-500">
                    Al usar nuestros servicios, aceptas estos términos y condiciones.
                </p>
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
