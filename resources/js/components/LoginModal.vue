<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = withDefaults(
    defineProps<{
        redirectTo?: string;
    }>(),
    {
        redirectTo: '/profile',
    },
);

const emit = defineEmits(['close']);

const showPassword = ref(false);
const rememberCredentials = ref(false);

const form = useForm({
    email: '',
    password: '',
});

const STORAGE_KEY = 'compay_saved_credentials';

onMounted(() => {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
        try {
            const { email, password } = JSON.parse(saved);
            form.email = email || '';
            form.password = password || '';
            rememberCredentials.value = true;
        } catch {
            localStorage.removeItem(STORAGE_KEY);
        }
    }
});

const submit = () => {
    if (rememberCredentials.value) {
        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify({
                email: form.email,
                password: form.password,
            }),
        );
    } else {
        localStorage.removeItem(STORAGE_KEY);
    }

    form.post('/login', {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(props.redirectTo);
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
        <div
            class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-slate-900"
        >
            <div class="flex items-center justify-between bg-primary px-6 py-4">
                <div>
                    <h2 class="text-lg font-bold text-white">Iniciar Sesión</h2>
                    <p class="text-xs text-white/80">
                        Accede a tu cuenta de Compay Market
                    </p>
                </div>
                <button
                    @click="$emit('close')"
                    class="flex size-7 items-center justify-center rounded-full bg-secondary text-secondary-foreground hover:bg-secondary/80"
                >
                    <svg
                        class="size-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-4 p-6">
                <!-- Google Login (visual only) -->
                <button
                    type="button"
                    class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white py-3.5 text-sm font-bold text-slate-700 transition-colors hover:bg-slate-50 dark:border-white/10 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                >
                    <svg class="size-5" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            fill="#EA4335"
                            d="M12 10.2v3.9h5.5c-.2 1.3-.8 2.4-1.8 3.1l2.9 2.2c1.7-1.5 2.6-3.8 2.6-6.5 0-.6-.1-1.2-.2-1.8H12z"
                        />
                        <path
                            fill="#34A853"
                            d="M12 21c2.4 0 4.5-.8 6-2.1l-2.9-2.2c-.8.5-1.8.8-3.1.8-2.4 0-4.5-1.6-5.2-3.9l-3 .2v2.3A9 9 0 0012 21z"
                        />
                        <path
                            fill="#4A90E2"
                            d="M6.8 13.6c-.2-.5-.3-1-.3-1.6s.1-1.1.3-1.6V8.1h-3A9 9 0 003 12c0 1.4.3 2.8.8 3.9l3-2.3z"
                        />
                        <path
                            fill="#FBBC05"
                            d="M12 6.4c1.3 0 2.5.5 3.4 1.3l2.5-2.5C16.5 3.9 14.4 3 12 3A9 9 0 003.8 8.1l3 2.3c.7-2.3 2.8-4 5.2-4z"
                        />
                    </svg>
                    Iniciar Sesión con Google
                </button>

                <div class="my-1.5 flex items-center gap-3">
                    <div
                        class="h-px flex-1 bg-slate-200 dark:bg-white/10"
                    ></div>
                    <span
                        class="text-xs font-medium text-slate-500 dark:text-slate-400"
                        >o con correo electrónico</span
                    >
                    <div
                        class="h-px flex-1 bg-slate-200 dark:bg-white/10"
                    ></div>
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label
                        class="text-sm font-bold text-slate-700 dark:text-slate-300"
                        >Correo Electrónico</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-white/10 dark:bg-slate-800 dark:text-white"
                        placeholder="tu@email.com"
                    />
                    <div v-if="form.errors.email" class="text-xs text-red-500">
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label
                        class="text-sm font-bold text-slate-700 dark:text-slate-300"
                        >Contraseña</label
                    >
                    <div class="relative">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-sm font-medium text-slate-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-white/10 dark:bg-slate-800 dark:text-white"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute top-1/2 right-3 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300"
                        >
                            <!-- Ojo abierto -->
                            <svg
                                v-if="!showPassword"
                                class="size-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                />
                            </svg>
                            <!-- Ojo cerrado -->
                            <svg
                                v-else
                                class="size-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Recordar credenciales -->
                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        v-model="rememberCredentials"
                        class="size-4 rounded border-slate-300 text-primary focus:ring-primary dark:border-white/20 dark:bg-slate-800"
                    />
                    <span class="text-sm text-slate-600 dark:text-slate-400"
                        >Recordar usuario y contraseña</span
                    >
                </label>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="mt-2 flex w-full items-center justify-center rounded-xl bg-primary py-3.5 font-bold text-white shadow-lg shadow-primary/30 transition-transform active:scale-[0.98] disabled:opacity-50 disabled:shadow-none"
                >
                    <span v-if="form.processing">Iniciando...</span>
                    <span v-else class="flex items-center gap-2">
                        <span
                            class="flex size-5 items-center justify-center rounded-full bg-secondary"
                        >
                            <svg
                                class="size-3 text-secondary-foreground"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="3"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </span>
                        Iniciar Sesión
                    </span>
                </button>
            </form>
        </div>
    </div>
</template>
