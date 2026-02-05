<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    HelpCircle,
    MessageCircleQuestion,
    Phone,
    FileText,
    Shield,
    Cookie,
    Scale,
} from 'lucide-vue-next';
import { computed } from 'vue';
import type { AppPageProps } from '@/types';

import MobileLayout from '@/layouts/MobileLayout.vue';

const page = usePage<AppPageProps>();
const settings = computed(() => page.props.settings);

interface HelpItem {
    id: string;
    title: string;
    description: string;
    icon: typeof HelpCircle;
    href: string;
    iconBgClass: string;
    iconColorClass: string;
}

const helpItems: HelpItem[] = [
    {
        id: 'faq',
        title: 'Preguntas Frecuentes',
        description: 'Encuentra respuestas a las dudas más comunes',
        icon: MessageCircleQuestion,
        href: '/help/faq',
        iconBgClass: 'bg-blue-50 dark:bg-blue-900/20',
        iconColorClass: 'text-blue-600 dark:text-blue-400',
    },
    {
        id: 'contact',
        title: 'Contacto',
        description: 'Comunícate con nuestro equipo de soporte',
        icon: Phone,
        href: '/help/contact',
        iconBgClass: 'bg-green-50 dark:bg-green-900/20',
        iconColorClass: 'text-green-600 dark:text-green-400',
    },
    {
        id: 'terms',
        title: 'Términos y Condiciones',
        description: 'Consulta nuestros términos de servicio',
        icon: FileText,
        href: '/help/terms',
        iconBgClass: 'bg-purple-50 dark:bg-purple-900/20',
        iconColorClass: 'text-purple-600 dark:text-purple-400',
    },
    {
        id: 'privacy',
        title: 'Políticas de Privacidad',
        description: 'Conoce cómo protegemos tus datos',
        icon: Shield,
        href: '/help/privacy',
        iconBgClass: 'bg-amber-50 dark:bg-amber-900/20',
        iconColorClass: 'text-amber-600 dark:text-amber-400',
    },
    {
        id: 'cookies',
        title: 'Políticas de Cookies',
        description: 'Información sobre el uso de cookies',
        icon: Cookie,
        href: '/help/cookies',
        iconBgClass: 'bg-orange-50 dark:bg-orange-900/20',
        iconColorClass: 'text-orange-600 dark:text-orange-400',
    },
    {
        id: 'legal',
        title: 'Aviso Legal',
        description: 'Información legal y regulatoria',
        icon: Scale,
        href: '/help/legal',
        iconBgClass: 'bg-slate-100 dark:bg-slate-800',
        iconColorClass: 'text-slate-600 dark:text-slate-400',
    },
];
</script>

<template>
    <Head title="Centro de Ayuda - Compay Market" />

    <MobileLayout active-nav="profile" :show-chat-button="false">
        <div
            class="flex flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- TopAppBar -->
            <header class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/profile"
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <ChevronLeft class="size-6 text-slate-900 dark:text-white" />
                        </Link>
                        <h1 class="text-lg font-bold">Centro de Ayuda</h1>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- Header Section -->
                <div class="flex flex-col items-center pb-6 text-center">
                    <div class="flex size-20 items-center justify-center rounded-full bg-primary/10">
                        <HelpCircle class="size-10 text-primary" />
                    </div>
                    <h2 class="mt-4 text-xl font-bold">¿En qué podemos ayudarte?</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Selecciona una opción para obtener más información
                    </p>
                </div>

                <!-- Help Items List -->
                <div class="flex flex-col gap-3">
                    <Link
                        v-for="item in helpItems"
                        :key="item.id"
                        :href="item.href"
                        class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm transition-all active:scale-[0.98] dark:bg-slate-800/50 border border-slate-200 dark:border-white/5"
                    >
                        <div
                            :class="[
                                'flex size-12 shrink-0 items-center justify-center rounded-xl',
                                item.iconBgClass,
                            ]"
                        >
                            <component :is="item.icon" :class="['size-6', item.iconColorClass]" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-slate-900 dark:text-white">
                                {{ item.title }}
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 truncate">
                                {{ item.description }}
                            </p>
                        </div>
                        <ChevronRight class="size-5 shrink-0 text-slate-400" />
                    </Link>
                </div>

                <!-- Contact Info Footer -->
                <div class="mt-8 rounded-2xl bg-primary/5 p-4 text-center dark:bg-primary/10">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        ¿Necesitas ayuda adicional?
                    </p>
                    <p class="mt-1 text-sm font-semibold text-primary">
                        {{ settings.email }}
                    </p>
                    <p v-if="settings.phone" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ settings.phone }}
                    </p>
                </div>
            </main>
        </div>
    </MobileLayout>
</template>
