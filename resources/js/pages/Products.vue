<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

type Product = {
    id: number;
    name: string;
    rating: number;
    reviews: number;
    price: number;
    image: string;
    isFavorite: boolean;
};

type FilterOption = {
    id: string;
    label: string;
    hasDropdown: boolean;
};

const props = defineProps<{
    category?: string;
}>();

const categoryTitle = computed(() => props.category || 'Electronics');

const filters = ref<FilterOption[]>([
    { id: 'filter', label: 'Filter', hasDropdown: false },
    { id: 'sort', label: 'Sort by', hasDropdown: true },
    { id: 'price', label: 'Price', hasDropdown: true },
    { id: 'brand', label: 'Brand', hasDropdown: true },
]);

const products = ref<Product[]>([
    {
        id: 1,
        name: 'Wireless Noise Cancelling...',
        rating: 4.8,
        reviews: 1200,
        price: 299.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDPbHOKA_HZlwqrUPMtNVQlYCT-wKHZxM7hHIWfZbqPBBpC8ePvbzqjxNFmBJCqUKI0qG-sKxE5g_7bGCl2ZB1Kw_I_ysHHHH-_mZG8M2DkLZKbzJoQK8YJZX3Q8w3F7wE2bN5oLw-X_vZJADFh8dU8Q_jzQw4Z5H7f0a5Y9K2M',
        isFavorite: true,
    },
    {
        id: 2,
        name: 'Smart Watch Series 7 Pro',
        rating: 4.9,
        reviews: 856,
        price: 399.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB8r0aiJ5ngCZEyqyTauHpLVAK6NNQC4pCB1S3mQJW5ceIfkdezlQeU5RZQf6CzLxQgtPxH89hDtVmPhTJxjw1CTwiThkijE1hOhLL2Uk8NWmaLTzrVNUtLdhqESUQFDYgbJKU_CLAXR_D1ml2RBVPiEmbDjDQ-J9Z6KNeBOAiG1XiEovRxgATGd5i5z2rJsbMcmkwTeBDfT4qyemWtjK46KtZbR4C3YM5yqIJDuTxlabMRGmBgxDwazyXghq9p07-kafO1Egn7o7g',
        isFavorite: false,
    },
    {
        id: 3,
        name: 'Portable Bass Speaker',
        rating: 4.5,
        reviews: 342,
        price: 89.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAHW_2qwFVlLcVN7OKLvOV9T0rXVPWNHZqYaAYnHO3YOz_VZ8Uxu_3DKPVfXZL2maBGDYOC6zrYVRJnQ8kW7HJ8Pp-7GVJE2M4k_Q-hZJwC8nQoSGR3-_a4wJHZ5Q9K1M',
        isFavorite: false,
    },
    {
        id: 4,
        name: 'DSLR Camera Kit 4K',
        rating: 4.7,
        reviews: 104,
        price: 1249.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO4f9Y-jqkXBYxkKrIgQT2ozhzYGez20ZAncUOIIpa4vOjsTFuIiyrEwNPGutfh0j83ghqFinrhrmQnGRA5oZbFP9mjrC5ezTwXFcmjNukRjGMoFFf0So2n4RNdKKj1VOTroo-omFNbzEizT9CJpdt3AUErlwQ5fYyeELD2bROrjaXt4lKZhn0rGkXxwVkMXEN3GgmcWhdYvPDRTX1MY3xGH8ZOte8Kb1Xy19CFnoeQ3FRIWqQrIqv4iBCVKkQcW9kpTHQ6jXoEMw',
        isFavorite: false,
    },
    {
        id: 5,
        name: 'Creative Tablet X Pro',
        rating: 4.8,
        reviews: 280,
        price: 649.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCx8jrIDsQet2gsCPjmllYeBU_c-nkpb_O0bu7E910IcOGwqTbGldHyC6AhFT2sHkACQ4x4cR2T9Mh1IInp8e5aBXI7jn6E0lyyHDGwVIHStqpDWJ9OOicbmpDhOEMZh_uxumz-MzCTqI2RFfHkOzH-UGFhnEpyKptYIrKFtQYFsTO5hFeXPhU3A3irp94FpYbrwyIpg7u9PKwp_yw7f-51g1cRVM88qLCJnpMb9-0zX67a9_dQKvGbsq9BEdT6CQ_OJ9QjGyd-3GQ',
        isFavorite: false,
    },
    {
        id: 6,
        name: 'Gaming Keyboard RGB',
        rating: 4.6,
        reviews: 523,
        price: 129.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBytzLFNJ1yvBG1fxEi5dIzbuOYzwyxiwGDjMtOwho5b2jndMNHJ5406TvFMQ4do_yHzimv_6oXV2LnakN2sB_SQrBwgD9OuKndorViorxx5a2zCHbPRpiV32VB1MNt6sQDBdnCrID04hu53fzp2zs30FrOduAsdBN9gCfQVScF3QOp6eQElc6JbwnBcjFwKCCCZA-IdE2G7ym_5nd7I_dmEPiqPQ2x2y_dVsbRiLPIQVXAl62IAFZRyb6AwUqUWaj-vPs190hiJrA',
        isFavorite: false,
    },
]);

const cartCount = ref(3);

function toggleFavorite(productId: number) {
    const product = products.value.find((p) => p.id === productId);
    if (product) {
        product.isFavorite = !product.isFavorite;
    }
}

function formatPrice(price: number): string {
    return `$${price.toFixed(2)}`;
}

function formatReviews(reviews: number): string {
    if (reviews >= 1000) {
        return `${(reviews / 1000).toFixed(1)}k`;
    }
    return reviews.toString();
}
</script>

<template>
    <Head :title="categoryTitle" />

    <MobileLayout active-nav="catalog" :cart-count="cartCount">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-slate-50 pb-2 pt-4 dark:bg-slate-900">
            <div class="mb-3 flex items-center justify-between px-4">
                <div class="flex items-center gap-3">
                    <Link
                        href="/"
                        class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                    >
                        <svg
                            class="size-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </Link>
                    <h1 class="text-lg font-bold">{{ categoryTitle }}</h1>
                </div>
                <button
                    class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                >
                    <svg
                        class="size-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </button>
            </div>

            <!-- Filter Chips -->
            <div class="hide-scrollbar flex gap-2 overflow-x-auto px-4 pb-2">
                <button
                    v-for="filter in filters"
                    :key="filter.id"
                    :class="[
                        'flex shrink-0 items-center gap-1 rounded-full border px-4 py-2 text-sm font-medium transition-colors',
                        filter.id === 'filter'
                            ? 'border-gray-200 bg-white dark:border-white/10 dark:bg-slate-800'
                            : 'border-gray-200 bg-gray-100 dark:border-white/10 dark:bg-slate-800',
                    ]"
                >
                    <svg
                        v-if="filter.id === 'filter'"
                        class="size-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                        />
                    </svg>
                    {{ filter.label }}
                    <svg
                        v-if="filter.hasDropdown"
                        class="size-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pb-24">
            <!-- Products Grid -->
            <div class="grid grid-cols-2 gap-4 py-4">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="flex flex-col rounded-2xl bg-white p-3 shadow-sm dark:bg-slate-800/50"
                >
                    <div
                        class="relative mb-3 aspect-square w-full overflow-hidden rounded-xl bg-gray-50 dark:bg-slate-700/50"
                    >
                        <Link :href="`/product/${product.id}`" class="block size-full">
                            <img
                                :src="product.image"
                                :alt="product.name"
                                class="size-full object-cover"
                            />
                        </Link>
                        <button
                            class="absolute right-2 top-2 flex size-8 items-center justify-center rounded-full bg-white/80 backdrop-blur dark:bg-slate-800/80"
                            @click.stop="toggleFavorite(product.id)"
                        >
                            <svg
                                :class="[
                                    'size-5',
                                    product.isFavorite
                                        ? 'fill-red-500 text-red-500'
                                        : 'text-gray-600 dark:text-gray-400',
                                ]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                />
                            </svg>
                        </button>
                    </div>

                    <Link :href="`/product/${product.id}`">
                        <h4 class="mb-1 line-clamp-2 text-sm font-bold leading-snug">
                            {{ product.name }}
                        </h4>
                    </Link>

                    <div class="mb-2 flex items-center gap-1">
                        <svg
                            class="size-4 fill-amber-400 text-amber-400"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                            />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                            {{ product.rating }} ({{ formatReviews(product.reviews) }})
                        </span>
                    </div>

                    <div class="mt-auto flex items-center justify-between">
                        <span class="text-lg font-extrabold text-blue-600">
                            {{ formatPrice(product.price) }}
                        </span>
                        <button
                            class="flex size-8 items-center justify-center rounded-lg bg-blue-600 text-white transition-colors hover:bg-blue-700"
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
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </main>

        </div>
    </MobileLayout>
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
