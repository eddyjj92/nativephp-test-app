<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

type FavoriteItem = {
    id: number;
    name: string;
    rating: number;
    reviews: number;
    price: number;
    image: string;
};

const favorites = ref<FavoriteItem[]>([
    {
        id: 1,
        name: 'Wireless Headphones',
        rating: 4.5,
        reviews: 120,
        price: 129.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBkdQgUIXRO2xmxQisdRa-xXNF5Xf60N8b147d5kkuAsHkR2B8sxIweopIOP4TQ-ZyafkU2CfP4ZqxpBxgSIVrL-hQXygWvNhurSpaxY1W29qELyh_NYKTClebstrSp-ZInNYMiAINV53-BKWqWJbK6YCaL1sphk566QGSEySsVrkdma9yrFh8Edcf_lzXyn431pIhFDUY8Wo7QHDbYZIIzeyKD0wibiAsBRfHXaOz5MwXWmY14QGmWO0YVxrtlKxPiUre2bKtYgks',
    },
    {
        id: 2,
        name: 'Smart Watch Series 5',
        rating: 4.8,
        reviews: 85,
        price: 299.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB8r0aiJ5ngCZEyqyTauHpLVAK6NNQC4pCB1S3mQJW5ceIfkdezlQeU5RZQf6CzLxQgtPxH89hDtVmPhTJxjw1CTwiThkijE1hOhLL2Uk8NWmaLTzrVNUtLdhqESUQFDYgbJKU_CLAXR_D1ml2RBVPiEmbDjDQ-J9Z6KNeBOAiG1XiEovRxgATGd5i5z2rJsbMcmkwTeBDfT4qyemWtjK46KtZbR4C3YM5yqIJDuTxlabMRGmBgxDwazyXghq9p07-kafO1Egn7o7g',
    },
    {
        id: 3,
        name: 'Minimalist Backpack',
        rating: 4.2,
        reviews: 42,
        price: 75.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO4f9Y-jqkXBYxkKrIgQT2ozhzYGez20ZAncUOIIpa4vOjsTFuIiyrEwNPGutfh0j83ghqFinrhrmQnGRA5oZbFP9mjrC5ezTwXFcmjNukRjGMoFFf0So2n4RNdKKj1VOTroo-omFNbzEizT9CJpdt3AUErlwQ5fYyeELD2bROrjaXt4lKZhn0rGkXxwVkMXEN3GgmcWhdYvPDRTX1MY3xGH8ZOte8Kb1Xy19CFnoeQ3FRIWqQrIqv4iBCVKkQcW9kpTHQ6jXoEMw',
    },
    {
        id: 4,
        name: 'Ergonomic Mouse',
        rating: 4.7,
        reviews: 210,
        price: 45.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCx8jrIDsQet2gsCPjmllYeBU_c-nkpb_O0bu7E910IcOGwqTbGldHyC6AhFT2sHkACQ4x4cR2T9Mh1IInp8e5aBXI7jn6E0lyyHDGwVIHStqpDWJ9OOicbmpDhOEMZh_uxumz-MzCTqI2RFfHkOzH-UGFhnEpyKptYIrKFtQYFsTO5hFeXPhU3A3irp94FpYbrwyIpg7u9PKwp_yw7f-51g1cRVM88qLCJnpMb9-0zX67a9_dQKvGbsq9BEdT6CQ_OJ9QjGyd-3GQ',
    },
]);

const cartCount = ref(2);

const itemCount = computed(() => favorites.value.length);

function formatPrice(price: number): string {
    const page = usePage();
    const currency = page.props.selectedCurrency as any;
    const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
    return `${symbol}${price.toFixed(2)}`;
}

function removeFromFavorites(itemId: number) {
    favorites.value = favorites.value.filter((item) => item.id !== itemId);
}

function addToCart(itemId: number) {
    cartCount.value++;
    console.log('Added to cart:', itemId);
}

function moveAllToCart() {
    cartCount.value += favorites.value.length;
    favorites.value = [];
}
</script>

<template>
    <Head title="Saved Items" />

    <MobileLayout active-nav="saved" :cart-count="cartCount">
        <div
            class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
        <!-- Header -->
        <header class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900">

            <div class="flex items-center justify-between px-4">
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
                    <h1 class="text-lg font-bold">Productos Guardados</h1>
                </div>
                <button
                    v-if="favorites.length > 0"
                    class="text-sm font-semibold text-blue-600"
                    @click="moveAllToCart"
                >
                    Mover todo al carrito
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
            <!-- Items Count -->
            <div v-if="favorites.length > 0" class="px-4 py-4">
                <p class="text-sm text-gray-500">
                    {{ itemCount }} producto{{ itemCount !== 1 ? 's' : '' }} guardados en su lista
                </p>
            </div>

            <!-- Empty State -->
            <div
                v-if="favorites.length === 0"
                class="flex flex-col items-center justify-center px-4 py-16"
            >
                <svg
                    class="mb-4 size-24 text-gray-300 dark:text-gray-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                    />
                </svg>
                <h2 class="mb-2 text-lg font-bold text-gray-600 dark:text-gray-400">
                    No saved items yet
                </h2>
                <p class="mb-6 text-center text-sm text-gray-400">
                    Items you save will appear here.<br />Start exploring and save your favorites!
                </p>
                <Link
                    href="/products"
                    class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white"
                >
                    Explore Products
                </Link>
            </div>

            <!-- Favorites List -->
            <div v-else class="flex flex-col gap-4 px-4">
                <div
                    v-for="item in favorites"
                    :key="item.id"
                    class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 dark:border-white/5 dark:bg-slate-800/50"
                >
                    <!-- Product Image -->
                    <Link
                        :href="`/product/${item.id}`"
                        class="relative size-28 shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-slate-700/50"
                    >
                        <img
                            :src="item.image"
                            :alt="item.name"
                            class="size-full object-cover"
                        />
                        <button
                            class="absolute right-2 top-2 flex size-7 items-center justify-center rounded-full bg-white/90 shadow-sm backdrop-blur"
                            @click.prevent="removeFromFavorites(item.id)"
                        >
                            <svg
                                class="size-4 fill-blue-600 text-blue-600"
                                fill="currentColor"
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
                    </Link>

                    <!-- Product Details -->
                    <div class="flex flex-1 flex-col">
                        <!-- Rating -->
                        <div class="mb-1 flex items-center gap-1">
                            <svg
                                class="size-4 fill-amber-400 text-amber-400"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                                />
                            </svg>
                            <span class="text-xs text-gray-500">
                                {{ item.rating }} ({{ item.reviews }} reviews)
                            </span>
                        </div>

                        <!-- Name -->
                        <Link :href="`/product/${item.id}`">
                            <h3 class="mb-1 font-bold leading-tight">{{ item.name }}</h3>
                        </Link>

                        <!-- Price -->
                        <span class="mb-3 text-lg font-extrabold text-blue-600">
                            {{ formatPrice(item.price) }}
                        </span>

                        <!-- Add to Cart Button -->
                        <button
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 py-2.5 text-sm font-bold text-white transition-colors hover:bg-blue-700"
                            @click="addToCart(item.id)"
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
                                    stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hint Text -->
            <div v-if="favorites.length > 0" class="px-4 py-6 text-center">
                <p class="text-sm italic text-gray-400">
                    Swipe left on any item to remove it from your list.
                </p>
            </div>
        </main>

        </div>
    </MobileLayout>
</template>
