<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ChevronLeft, ChevronDown, MessageCircleQuestion } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { AppPageProps, FrequentlyAskedQuestion } from '@/types';

import MobileLayout from '@/layouts/MobileLayout.vue';

const page = usePage<AppPageProps>();
const settings = computed(() => page.props.settings);
const faqs = computed<FrequentlyAskedQuestion[]>(() => settings.value.frequently_questions ?? []);

const expandedIndex = ref<number | null>(null);

function toggleFaq(index: number) {
    expandedIndex.value = expandedIndex.value === index ? null : index;
}
</script>

<template>
    <Head title="Preguntas Frecuentes - Compay Market" />

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
                        <h1 class="text-lg font-bold">Preguntas Frecuentes</h1>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- Header Section -->
                <div class="flex flex-col items-center pb-6 text-center">
                    <div class="flex size-16 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/20">
                        <MessageCircleQuestion class="size-8 text-primary dark:text-blue-400" />
                    </div>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        Encuentra respuestas a las dudas más comunes
                    </p>
                </div>

                <!-- FAQ List -->
                <div v-if="faqs.length > 0" class="flex flex-col gap-3">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5"
                    >
                        <button
                            @click="toggleFaq(index)"
                            class="flex w-full items-center justify-between p-4 text-left transition-colors hover:bg-slate-50 dark:hover:bg-slate-800"
                        >
                            <span class="font-semibold text-slate-900 dark:text-white pr-4">
                                {{ faq.question }}
                            </span>
                            <ChevronDown
                                :class="[
                                    'size-5 shrink-0 text-slate-400 transition-transform duration-200',
                                    expandedIndex === index ? 'rotate-180' : '',
                                ]"
                            />
                        </button>
                        <Transition
                            enter-active-class="transition-all duration-200 ease-out"
                            enter-from-class="opacity-0 max-h-0"
                            enter-to-class="opacity-100 max-h-96"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 max-h-96"
                            leave-to-class="opacity-0 max-h-0"
                        >
                            <div
                                v-show="expandedIndex === index"
                                class="border-t border-slate-100 dark:border-white/5"
                            >
                                <p class="p-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                    {{ faq.answer }}
                                </p>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center py-12 text-center"
                >
                    <MessageCircleQuestion class="size-16 text-slate-300 dark:text-slate-600" />
                    <h3 class="mt-4 font-semibold text-slate-700 dark:text-slate-300">
                        Sin preguntas frecuentes
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Aún no hay preguntas frecuentes disponibles
                    </p>
                </div>

                <!-- Contact CTA -->
                <div class="mt-8 rounded-2xl bg-blue-50 p-4 text-center dark:bg-blue-900/20">
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        ¿No encontraste lo que buscabas?
                    </p>
                    <Link
                        href="/help/contact"
                        class="mt-2 inline-block text-sm font-semibold text-primary dark:text-blue-400"
                    >
                        Contáctanos →
                    </Link>
                </div>
            </main>
        </div>
    </MobileLayout>
</template>
