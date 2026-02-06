<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import { CheckCircle, XCircle, Info, AlertTriangle, X } from 'lucide-vue-next';
import { ref } from 'vue';

const { toasts, removeToast } = useToast();

const swipeState = ref<{
    [key: number]: { startX: number; currentX: number; swiping: boolean };
}>({});

const onTouchStart = (id: number, event: TouchEvent) => {
    swipeState.value[id] = {
        startX: event.touches[0].clientX,
        currentX: 0,
        swiping: true,
    };
};

const onTouchMove = (id: number, event: TouchEvent) => {
    if (!swipeState.value[id]?.swiping) return;
    const deltaX = event.touches[0].clientX - swipeState.value[id].startX;
    swipeState.value[id].currentX = deltaX;
};

const onTouchEnd = (id: number) => {
    if (!swipeState.value[id]) return;
    const threshold = 80;
    if (Math.abs(swipeState.value[id].currentX) > threshold) {
        removeToast(id);
    }
    delete swipeState.value[id];
};

const getSwipeTransform = (id: number) => {
    const state = swipeState.value[id];
    if (!state?.swiping) return '';
    return `translateX(${state.currentX}px)`;
};

const getSwipeOpacity = (id: number) => {
    const state = swipeState.value[id];
    if (!state?.swiping) return 1;
    const opacity = 1 - Math.abs(state.currentX) / 150;
    return Math.max(0.3, opacity);
};

const getIcon = (type: string) => {
    switch (type) {
        case 'success':
            return CheckCircle;
        case 'error':
            return XCircle;
        case 'warning':
            return AlertTriangle;
        default:
            return Info;
    }
};

const getStyles = (type: string) => {
    switch (type) {
        case 'success':
            return 'bg-green-50 dark:bg-green-950/50 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200';
        case 'error':
            return 'bg-red-50 dark:bg-red-950/50 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200';
        case 'warning':
            return 'bg-yellow-50 dark:bg-yellow-950/50 border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200';
        default:
            return 'bg-blue-50 dark:bg-blue-950/50 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200';
    }
};

const getIconColor = (type: string) => {
    switch (type) {
        case 'success':
            return 'text-green-500';
        case 'error':
            return 'text-red-500';
        case 'warning':
            return 'text-yellow-500';
        default:
            return 'text-blue-500';
    }
};
</script>

<template>
    <Teleport to="body">
        <div
            class="pointer-events-none fixed top-[calc(var(--inset-top,0px)+12px)] right-0 left-0 z-[200] flex flex-col items-center gap-2 px-4"
        >
            <TransitionGroup
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-4 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 -translate-y-2 scale-95"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="[
                        'pointer-events-auto flex w-full max-w-sm cursor-grab touch-pan-y items-center gap-3 rounded-xl border px-4 py-3 shadow-lg backdrop-blur-sm active:cursor-grabbing',
                        getStyles(toast.type),
                    ]"
                    :style="{
                        transform: getSwipeTransform(toast.id) || undefined,
                        opacity: swipeState[toast.id] ? getSwipeOpacity(toast.id) : undefined,
                        transition: swipeState[toast.id]
                            ? swipeState[toast.id].swiping
                                ? 'none'
                                : 'transform 0.2s ease-out, opacity 0.2s ease-out'
                            : undefined,
                    }"
                    @touchstart="onTouchStart(toast.id, $event)"
                    @touchmove="onTouchMove(toast.id, $event)"
                    @touchend="onTouchEnd(toast.id)"
                >
                    <component
                        :is="getIcon(toast.type)"
                        :class="['size-5 shrink-0', getIconColor(toast.type)]"
                    />
                    <p class="flex-1 text-sm font-medium">
                        {{ toast.message }}
                    </p>
                    <button
                        @click="removeToast(toast.id)"
                        class="shrink-0 rounded-full p-1 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                    >
                        <X class="size-4" />
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
