<script setup lang="ts">
defineProps<{
    message?: string;
}>();

const emit = defineEmits<{
    retry: [];
}>();

function handleRetry() {
    emit('retry');
}
</script>

<template>
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-50 dark:bg-slate-900"
    >
        <div class="flex flex-col items-center px-8 text-center">
            <!-- Icon -->
            <div
                class="mb-6 flex size-24 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30"
            >
                <svg
                    class="size-12 text-red-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M18.364 5.636a9 9 0 11-12.728 0M12 9v4m0 4h.01"
                    />
                </svg>
            </div>

            <!-- Title -->
            <h2 class="mb-2 text-xl font-bold text-slate-900 dark:text-white">
                Sin conexión
            </h2>

            <!-- Message -->
            <p class="mb-8 text-sm text-slate-500 dark:text-slate-400">
                {{
                    message ||
                    'No se pudo conectar con el servidor. Verifica tu conexión a internet e intenta de nuevo.'
                }}
            </p>

            <!-- Retry Button -->
            <button
                class="flex items-center gap-2 rounded-full bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-700 active:scale-95"
                @click="handleRetry"
            >
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
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                    />
                </svg>
                Reintentar
            </button>
        </div>
    </div>
</template>
