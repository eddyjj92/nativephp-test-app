<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

type ColorOption = {
    id: string;
    name: string;
    color: string;
};

type Product = {
    id: number;
    name: string;
    price: number;
    rating: number;
    reviews: number;
    description: string;
    images: string[];
    colors: ColorOption[];
    stock: number;
    deliveryDate: string;
};

const props = defineProps<{
    productId?: number;
}>();

const product = ref<Product>({
    id: props.productId || 1,
    name: 'iPhone 15 Pro Max',
    price: 1199.0,
    rating: 4.9,
    reviews: 2400,
    description:
        'Experience the power of the new A17 Pro chip, a titanium design, and a versatile Pro camera system. Built for performance and designed to last.',
    images: [
        'https://lh3.googleusercontent.com/aida-public/AB6AXuB8r0aiJ5ngCZEyqyTauHpLVAK6NNQC4pCB1S3mQJW5ceIfkdezlQeU5RZQf6CzLxQgtPxH89hDtVmPhTJxjw1CTwiThkijE1hOhLL2Uk8NWmaLTzrVNUtLdhqESUQFDYgbJKU_CLAXR_D1ml2RBVPiEmbDjDQ-J9Z6KNeBOAiG1XiEovRxgATGd5i5z2rJsbMcmkwTeBDfT4qyemWtjK46KtZbR4C3YM5yqIJDuTxlabMRGmBgxDwazyXghq9p07-kafO1Egn7o7g',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuBkdQgUIXRO2xmxQisdRa-xXNF5Xf60N8b147d5kkuAsHkR2B8sxIweopIOP4TQ-ZyafkU2CfP4ZqxpBxgSIVrL-hQXygWvNhurSpaxY1W29qELyh_NYKTClebstrSp-ZInNYMiAINV53-BKWqWJbK6YCaL1sphk566QGSEySsVrkdma9yrFh8Edcf_lzXyn431pIhFDUY8Wo7QHDbYZIIzeyKD0wibiAsBRfHXaOz5MwXWmY14QGmWO0YVxrtlKxPiUre2bKtYgks',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuCO4f9Y-jqkXBYxkKrIgQT2ozhzYGez20ZAncUOIIpa4vOjsTFuIiyrEwNPGutfh0j83ghqFinrhrmQnGRA5oZbFP9mjrC5ezTwXFcmjNukRjGMoFFf0So2n4RNdKKj1VOTroo-omFNbzEizT9CJpdt3AUErlwQ5fYyeELD2bROrjaXt4lKZhn0rGkXxwVkMXEN3GgmcWhdYvPDRTX1MY3xGH8ZOte8Kb1Xy19CFnoeQ3FRIWqQrIqv4iBCVKkQcW9kpTHQ6jXoEMw',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuCx8jrIDsQet2gsCPjmllYeBU_c-nkpb_O0bu7E910IcOGwqTbGldHyC6AhFT2sHkACQ4x4cR2T9Mh1IInp8e5aBXI7jn6E0lyyHDGwVIHStqpDWJ9OOicbmpDhOEMZh_uxumz-MzCTqI2RFfHkOzH-UGFhnEpyKptYIrKFtQYFsTO5hFeXPhU3A3irp94FpYbrwyIpg7u9PKwp_yw7f-51g1cRVM88qLCJnpMb9-0zX67a9_dQKvGbsq9BEdT6CQ_OJ9QjGyd-3GQ',
    ],
    colors: [
        { id: 'titanium', name: 'Titanium', color: '#3d3d3d' },
        { id: 'black', name: 'Black', color: '#6b6b6b' },
        { id: 'white', name: 'White', color: '#e8e8e8' },
    ],
    stock: 12,
    deliveryDate: 'Tomorrow, Oct 24',
});

const currentImageIndex = ref(0);
const selectedColor = ref('titanium');
const quantity = ref(1);
const isFavorite = ref(false);
const showFullDescription = ref(false);
const cartCount = ref(3);

const formattedPrice = computed(() => `$${product.value.price.toFixed(2)}`);

const formattedReviews = computed(() => {
    const reviews = product.value.reviews;
    if (reviews >= 1000) {
        return `${(reviews / 1000).toFixed(1)}k reviews`;
    }
    return `${reviews} reviews`;
});

function incrementQuantity() {
    if (quantity.value < product.value.stock) {
        quantity.value++;
    }
}

function decrementQuantity() {
    if (quantity.value > 1) {
        quantity.value--;
    }
}

function toggleFavorite() {
    isFavorite.value = !isFavorite.value;
}

function addToCart() {
    cartCount.value += quantity.value;
}

function goToImage(index: number) {
    currentImageIndex.value = index;
}
</script>

<template>
    <Head :title="product.name" />

    <MobileLayout active-nav="catalog" :cart-count="cartCount">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
        <!-- Header -->
        <header
            class="sticky top-0 z-50 bg-slate-50 py-4 dark:bg-slate-900"
        >
            <div class="flex items-center justify-between px-4">
                <div class="flex items-center gap-3">
                    <Link
                        href="/products"
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
                    <h1 class="text-lg font-bold">Product Details</h1>
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
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
                        />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 pb-40">
            <!-- Product Image Carousel -->
            <div class="relative aspect-square w-full bg-gray-900">
                <img
                    :src="product.images[currentImageIndex]"
                    :alt="product.name"
                    class="size-full object-cover"
                />
                <!-- Carousel Dots -->
                <div class="absolute inset-x-0 bottom-4 flex justify-center gap-2">
                    <button
                        v-for="(_, index) in product.images"
                        :key="index"
                        :class="[
                            'size-2 rounded-full transition-colors',
                            currentImageIndex === index
                                ? 'bg-blue-600'
                                : 'bg-white/50',
                        ]"
                        @click="goToImage(index)"
                    />
                </div>
            </div>

            <!-- Product Info -->
            <div class="px-4 py-6">
                <!-- Name & Price -->
                <div class="mb-4 flex items-start justify-between gap-4">
                    <h2 class="text-2xl font-bold leading-tight">{{ product.name }}</h2>
                    <span
                        class="shrink-0 rounded-full bg-blue-600 px-4 py-2 text-sm font-bold text-white"
                    >
                        {{ formattedPrice }}
                    </span>
                </div>

                <!-- Rating -->
                <div class="mb-6 flex items-center gap-2">
                    <svg class="size-5 fill-amber-400 text-amber-400" viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                        />
                    </svg>
                    <span class="font-semibold">{{ product.rating }}</span>
                    <span class="text-sm text-gray-400">({{ formattedReviews }})</span>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h3
                        class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400"
                    >
                        Description
                    </h3>
                    <p
                        :class="[
                            'text-sm leading-relaxed text-gray-600 dark:text-gray-300',
                            !showFullDescription && 'line-clamp-3',
                        ]"
                    >
                        {{ product.description }}
                    </p>
                    <button
                        class="mt-1 text-sm font-semibold text-blue-600"
                        @click="showFullDescription = !showFullDescription"
                    >
                        {{ showFullDescription ? 'Show less' : 'Read more' }}
                    </button>
                </div>

                <!-- Color Selection -->
                <div class="mb-6">
                    <h3
                        class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400"
                    >
                        Select Color
                    </h3>
                    <div class="flex gap-4">
                        <button
                            v-for="color in product.colors"
                            :key="color.id"
                            class="flex flex-col items-center gap-2"
                            @click="selectedColor = color.id"
                        >
                            <div
                                :class="[
                                    'flex size-12 items-center justify-center rounded-full',
                                    selectedColor === color.id
                                        ? 'ring-2 ring-blue-600 ring-offset-2 dark:ring-offset-slate-900'
                                        : '',
                                ]"
                                :style="{ backgroundColor: color.color }"
                            />
                            <span
                                :class="[
                                    'text-xs font-medium',
                                    selectedColor === color.id
                                        ? 'text-blue-600'
                                        : 'text-gray-500',
                                ]"
                            >
                                {{ color.name }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="mb-6">
                    <h3
                        class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400"
                    >
                        Quantity
                    </h3>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-green-600">
                            In Stock ({{ product.stock }} units)
                        </span>
                        <div class="flex items-center gap-3">
                            <button
                                class="flex size-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition-colors hover:bg-gray-50 dark:border-white/10 dark:text-white dark:hover:bg-slate-800"
                                @click="decrementQuantity"
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
                                        d="M20 12H4"
                                    />
                                </svg>
                            </button>
                            <span class="w-8 text-center text-lg font-bold">
                                {{ quantity }}
                            </span>
                            <button
                                class="flex size-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition-colors hover:bg-gray-50 dark:border-white/10 dark:text-white dark:hover:bg-slate-800"
                                @click="incrementQuantity"
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

                <!-- Delivery Info -->
                <div
                    class="flex items-center gap-4 rounded-xl border border-gray-100 bg-white p-4 dark:border-white/5 dark:bg-slate-800/50"
                >
                    <div
                        class="flex size-12 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/30"
                    >
                        <svg
                            class="size-6 text-blue-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"
                            />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold">Free Fast Delivery</h4>
                        <p class="text-sm text-gray-400">
                            Estimated delivery: {{ product.deliveryDate }}
                        </p>
                    </div>
                    <svg
                        class="size-5 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </div>
            </div>
        </main>

        <!-- Bottom Action Bar -->
        <div
            class="fixed inset-x-0 bottom-16 z-40 border-t border-gray-100 bg-white px-4 py-4 dark:border-white/5 dark:bg-slate-900"
        >
            <div class="flex items-center gap-3">
                <button
                    :class="[
                        'flex size-14 items-center justify-center rounded-xl border transition-colors',
                        isFavorite
                            ? 'border-red-200 bg-red-50 dark:border-red-900/50 dark:bg-red-900/20'
                            : 'border-gray-200 bg-white dark:border-white/10 dark:bg-slate-800',
                    ]"
                    @click="toggleFavorite"
                >
                    <svg
                        :class="[
                            'size-6',
                            isFavorite ? 'fill-red-500 text-red-500' : 'text-gray-600 dark:text-gray-400',
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
                <button
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 py-4 font-bold text-white shadow-lg shadow-blue-600/30 transition-colors hover:bg-blue-700"
                    @click="addToCart"
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
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                        />
                    </svg>
                    Add to Cart
                </button>
            </div>
        </div>

        </div>
    </MobileLayout>
</template>
