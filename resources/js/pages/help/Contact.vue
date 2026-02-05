<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ChevronLeft,
    Phone,
    Mail,
    MapPin,
    ExternalLink,
} from 'lucide-vue-next';
import { computed } from 'vue';
import type { AppPageProps } from '@/types';

import MobileLayout from '@/layouts/MobileLayout.vue';

const page = usePage<AppPageProps>();
const settings = computed(() => page.props.settings);

function openExternalLink(url: string | null) {
    if (url) {
        window.open(url, '_blank');
    }
}
</script>

<template>
    <Head title="Contacto - Compay Market" />

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
                        <h1 class="text-lg font-bold">Contacto</h1>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- Header Section -->
                <div class="flex flex-col items-center pb-6 text-center">
                    <div class="flex size-16 items-center justify-center rounded-full bg-green-50 dark:bg-green-900/20">
                        <Phone class="size-8 text-green-600 dark:text-green-400" />
                    </div>
                    <h2 class="mt-4 text-xl font-bold">Contáctanos</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Estamos aquí para ayudarte
                    </p>
                </div>

                <!-- Contact Info Cards -->
                <div class="flex flex-col gap-4">
                    <!-- Email -->
                    <a
                        :href="`mailto:${settings.email}`"
                        class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm transition-all active:scale-[0.98] dark:bg-slate-800/50 border border-slate-200 dark:border-white/5"
                    >
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/20">
                            <Mail class="size-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Correo Electrónico
                            </h3>
                            <p class="font-semibold text-slate-900 dark:text-white truncate">
                                {{ settings.email }}
                            </p>
                        </div>
                        <ExternalLink class="size-5 shrink-0 text-slate-400" />
                    </a>

                    <!-- Phone -->
                    <a
                        v-if="settings.phone"
                        :href="`tel:${settings.phone}`"
                        class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm transition-all active:scale-[0.98] dark:bg-slate-800/50 border border-slate-200 dark:border-white/5"
                    >
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-green-50 dark:bg-green-900/20">
                            <Phone class="size-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Teléfono
                            </h3>
                            <p class="font-semibold text-slate-900 dark:text-white">
                                {{ settings.phone }}
                            </p>
                        </div>
                        <ExternalLink class="size-5 shrink-0 text-slate-400" />
                    </a>

                    <!-- Address -->
                    <div
                        class="flex items-start gap-4 rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5"
                    >
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/20">
                            <MapPin class="size-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Dirección
                            </h3>
                            <p class="font-semibold text-slate-900 dark:text-white">
                                {{ settings.address }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div v-if="settings.facebook || settings.instagram || settings.x" class="mt-8">
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Redes Sociales
                    </h3>
                    <div class="flex gap-3">
                        <button
                            v-if="settings.facebook"
                            @click="openExternalLink(settings.facebook)"
                            class="flex size-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/30 transition-transform active:scale-95"
                        >
                            <svg class="size-7" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </button>
                        <button
                            v-if="settings.instagram"
                            @click="openExternalLink(settings.instagram)"
                            class="flex size-14 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-600 via-pink-500 to-orange-400 text-white shadow-lg shadow-pink-500/30 transition-transform active:scale-95"
                        >
                            <svg class="size-7" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </button>
                        <button
                            v-if="settings.x"
                            @click="openExternalLink(settings.x)"
                            class="flex size-14 items-center justify-center rounded-2xl bg-black text-white shadow-lg shadow-black/30 transition-transform active:scale-95 dark:bg-white dark:text-black"
                        >
                            <svg class="size-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Business Hours Info -->
                <div class="mt-8 rounded-2xl bg-green-50 p-4 dark:bg-green-900/20">
                    <h4 class="font-semibold text-green-800 dark:text-green-300">
                        Horario de Atención
                    </h4>
                    <p class="mt-1 text-sm text-green-700 dark:text-green-400">
                        Lunes a Viernes: 9:00 AM - 6:00 PM<br />
                        Sábados: 9:00 AM - 1:00 PM
                    </p>
                </div>
            </main>
        </div>
    </MobileLayout>
</template>
