<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';

const emit = defineEmits(['close']);

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post('/login-api', {
        preserveScroll: true,
        onSuccess: () => {
            router.visit('/profile')
            emit('close');
        },
    });
};
</script>

<template>
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click.self="$emit('close')"
    >
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-slate-900">
            <div class="flex items-center justify-between bg-blue-600 px-6 py-4">
                <div>
                    <h2 class="text-lg font-bold text-white">Iniciar Sesión</h2>
                    <p class="text-xs text-blue-100">Accede a tu cuenta de Compay Market</p>
                </div>
                <button @click="$emit('close')" class="rounded-full bg-blue-500 p-1 text-white hover:bg-blue-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-4 p-6">
                <!-- Email -->
                <div class="space-y-1">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Correo Electrónico</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-white/10 dark:bg-slate-800 dark:text-white"
                        placeholder="tu@email.com"
                    />
                    <div v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</div>
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Contraseña</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-white/10 dark:bg-slate-800 dark:text-white"
                        placeholder="••••••••"
                    />
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="mt-2 flex w-full items-center justify-center rounded-xl bg-blue-600 py-3.5 font-bold text-white shadow-lg shadow-blue-600/30 transition-transform active:scale-[0.98] disabled:opacity-50 disabled:shadow-none"
                >
                    <span v-if="form.processing">Iniciando...</span>
                    <span v-else>Entrar</span>
                </button>
            </form>
        </div>
    </div>
</template>
