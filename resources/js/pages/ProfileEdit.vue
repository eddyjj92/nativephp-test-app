<script setup lang="ts">
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import {
    ChevronLeft,
    User,
    Mail,
    Phone,
    MapPin,
    Save,
    Loader2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';

import MobileLayout from '@/layouts/MobileLayout.vue';

const page = usePage();
const user = computed(() => (page.props.auth as any)?.user ?? null);

const form = useForm({
    name: user.value?.name ?? '',
    email: user.value?.email ?? '',
    phone_country_code: user.value?.phone_country_code ?? '+53',
    phone: user.value?.phone ?? '',
    address: user.value?.address ?? '',
});

function getInitials(name: string = '') {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
}

function submit() {
    form.post('/profile/info/update', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Información Personal - Compay Market" />

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
                        <h1 class="text-lg font-bold">Información Personal</h1>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- Profile Header -->
                <div class="flex flex-col items-center pb-6">
                    <Avatar class="size-24 border-4 border-primary/10">
                        <AvatarImage
                            v-if="user?.avatar"
                            :src="user.avatar"
                            :alt="user.name"
                            class="object-cover"
                        />
                        <AvatarFallback class="text-2xl bg-slate-100 dark:bg-slate-800 font-bold text-slate-500">
                            {{ getInitials(user?.name) }}
                        </AvatarFallback>
                    </Avatar>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Actualiza tu información personal
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Name -->
                    <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5">
                        <Label for="name" class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <User class="size-4 text-primary" />
                            Nombre Completo
                        </Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="Tu nombre completo"
                            class="h-12 rounded-xl border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-slate-900/50"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>

                    <!-- Email -->
                    <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5">
                        <Label for="email" class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <Mail class="size-4 text-primary" />
                            Correo Electrónico
                        </Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="tu@email.com"
                            class="h-12 rounded-xl border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-slate-900/50"
                            required
                        />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Phone -->
                    <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5">
                        <Label for="phone" class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <Phone class="size-4 text-primary" />
                            Teléfono
                        </Label>
                        <div class="flex gap-2">
                            <Input
                                id="phone_country_code"
                                v-model="form.phone_country_code"
                                type="text"
                                placeholder="+53"
                                class="h-12 w-20 rounded-xl border-slate-200 bg-slate-50 text-center dark:border-white/10 dark:bg-slate-900/50"
                            />
                            <Input
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                placeholder="Tu número de teléfono"
                                class="h-12 flex-1 rounded-xl border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-slate-900/50"
                            />
                        </div>
                        <InputError :message="form.errors.phone" class="mt-1" />
                        <InputError :message="form.errors.phone_country_code" class="mt-1" />
                    </div>

                    <!-- Address -->
                    <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5">
                        <Label for="address" class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <MapPin class="size-4 text-primary" />
                            Dirección
                        </Label>
                        <textarea
                            id="address"
                            v-model="form.address"
                            rows="3"
                            placeholder="Tu dirección completa"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-base outline-none transition-colors placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-white/10 dark:bg-slate-900/50 dark:placeholder:text-slate-500"
                        ></textarea>
                        <InputError :message="form.errors.address" class="mt-1" />
                    </div>

                    <!-- Success Message -->
                    <Transition
                        enter-active-class="transition ease-in-out duration-300"
                        enter-from-class="opacity-0 translate-y-2"
                        leave-active-class="transition ease-in-out duration-200"
                        leave-to-class="opacity-0"
                    >
                        <div
                            v-if="form.recentlySuccessful"
                            class="rounded-xl bg-green-50 dark:bg-green-900/20 p-4 text-center text-sm font-medium text-green-600 dark:text-green-400"
                        >
                            ¡Información actualizada correctamente!
                        </div>
                    </Transition>
                </form>
            </main>

            <!-- Bottom Action Bar -->
            <div
                class="inset-x-0 bottom-0 z-40 border-t border-slate-100 bg-white px-4 pb-[calc(var(--inset-bottom,0px)+1rem)] pt-4 dark:border-white/5 dark:bg-slate-900"
            >
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-4 font-bold text-white shadow-lg shadow-primary/30 transition-all hover:bg-primary/90 active:scale-[0.98] disabled:opacity-50 disabled:shadow-none dark:text-black"
                >
                    <Loader2 v-if="form.processing" class="size-5 animate-spin" />
                    <Save v-else class="size-5" />
                    <span>{{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}</span>
                </button>
            </div>
        </div>
    </MobileLayout>
</template>
