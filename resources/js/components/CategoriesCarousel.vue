<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref, watch, onUnmounted } from 'vue';
import type { Category } from '@/types';

const props = defineProps<{
    categories: Category[];
    nextPageUrl?: string | null;
}>();

const isLoading = ref(false);
const triggerRef = ref<HTMLElement | null>(null);
let observer: IntersectionObserver | null = null;

function loadMore() {
    if (!props.nextPageUrl || isLoading.value) return;

    isLoading.value = true;

    router.get(props.nextPageUrl, {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['categories', 'categoriesNextPageUrl'],
        onFinish: () => {
            isLoading.value = false;
        },
    });
}

// Set up intersection observer for infinite scroll trigger
watch(triggerRef, (el, _, onCleanup) => {
    if (observer) {
        observer.disconnect();
        observer = null;
    }

    if (!el) return;

    observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting) {
                loadMore();
            }
        },
        { threshold: 0.1, rootMargin: '100px' }
    );

    observer.observe(el);

    onCleanup(() => {
        if (observer) {
            observer.disconnect();
            observer = null;
        }
    });
}, { immediate: true });

onUnmounted(() => {
    if (observer) {
        observer.disconnect();
        observer = null;
    }
});
</script>

<template>
    <div class="mb-2 mt-2 flex items-center justify-between px-4">
        <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">
            Categories
        </h3>
        <Link
            href="/products"
            class="text-xs font-bold uppercase tracking-wider text-blue-600"
        >
            See All
        </Link>
    </div>

    <div class="hide-scrollbar mb-4 flex gap-4 overflow-x-auto px-4 py-2">
        <Link
            v-for="category in categories"
            :key="category.id"
            :href="`/products?category_id=${category.id}`"
            class="flex min-w-[70px] flex-col items-center gap-2"
        >
            <div
                class="flex size-16 items-center justify-center rounded-full bg-white p-2 shadow-sm dark:bg-slate-800/50"
            >
                <img
                    :src="category.image"
                    :alt="category.name"
                    class="size-full object-contain"
                />
            </div>
            <p class="line-clamp-2 text-center text-[10px] font-bold leading-tight text-gray-700 dark:text-gray-300">
                {{ category.name }}
            </p>
        </Link>

        <!-- Infinite Scroll Trigger with Bouncing Dots -->
        <div
            v-if="nextPageUrl"
            ref="triggerRef"
            class="flex min-w-[70px] items-center justify-center"
        >
            <div class="flex items-center gap-1">
                <span class="size-2 animate-bounce rounded-full bg-blue-600 [animation-delay:-0.3s]"></span>
                <span class="size-2 animate-bounce rounded-full bg-blue-600 [animation-delay:-0.15s]"></span>
                <span class="size-2 animate-bounce rounded-full bg-blue-600"></span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
