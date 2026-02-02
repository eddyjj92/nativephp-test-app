<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { WhenVisible } from '@inertiajs/vue3';
import type { Category } from '@/types';

defineProps<{
    categories: Category[];
    nextPageUrl?: string | null;
}>();
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
            :href="`/products/${category.slug}`"
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
            <p class="text-center text-[10px] font-bold leading-tight text-gray-700 dark:text-gray-300 line-clamp-2">
                {{ category.name }}
            </p>
        </Link>

        <!-- Infinite Scroll Trigger -->
        <WhenVisible
            v-if="nextPageUrl"
            :href="nextPageUrl"
            :only="['categories', 'categoriesNextPageUrl']"
            class="flex min-w-[20px] items-center justify-center"
        >
            <div class="h-6 w-6 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></div>
        </WhenVisible>
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
