<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';
import ProductSkeleton from '@/components/ProductSkeleton.vue';

type Category = {
    id: string;
    name: string;
    icon: string;
};

type Product = {
    id: number;
    name: string;
    brand: string;
    category: string;
    price: number;
    originalPrice?: number;
    image: string;
    isFavorite: boolean;
};

type Banner = {
    id: number;
    badge: string;
    badgeStyle: 'primary' | 'white';
    title: string;
    subtitle: string;
    buttonText: string;
    buttonStyle: 'primary' | 'white';
    image: string;
};

const page = usePage();
const locationMissing = computed(() => !!page.props.showLocationModal);

const carouselRef = ref<HTMLElement | null>(null);
const activeBannerIndex = ref(0);
let autoplayTimer: ReturnType<typeof setInterval> | null = null;

const categories = ref<Category[]>([
    { id: 'electronics', name: 'Electronics', icon: 'devices' },
    { id: 'fashion', name: 'Fashion', icon: 'apparel' },
    { id: 'home', name: 'Home', icon: 'home' },
    { id: 'groceries', name: 'Groceries', icon: 'restaurant' },
    { id: 'sports', name: 'Sports', icon: 'fitness_center' },
]);

const selectedCategory = ref<string>('electronics');

const banners = ref<Banner[]>([
    {
        id: 1,
        badge: 'Limited Time',
        badgeStyle: 'primary',
        title: 'Tech Upgrade\nSale - 40% OFF',
        subtitle: 'Latest gadgets and accessories',
        buttonText: 'Shop Now',
        buttonStyle: 'primary',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBytzLFNJ1yvBG1fxEi5dIzbuOYzwyxiwGDjMtOwho5b2jndMNHJ5406TvFMQ4do_yHzimv_6oXV2LnakN2sB_SQrBwgD9OuKndorViorxx5a2zCHbPRpiV32VB1MNt6sQDBdnCrID04hu53fzp2zs30FrOduAsdBN9gCfQVScF3QOp6eQElc6JbwnBcjFwKCCCZA-IdE2G7ym_5nd7I_dmEPiqPQ2x2y_dVsbRiLPIQVXAl62IAFZRyb6AwUqUWaj-vPs190hiJrA',
    },
    {
        id: 2,
        badge: 'New Season',
        badgeStyle: 'white',
        title: 'Summer Collection\nArrived',
        subtitle: 'Discover the latest fashion trends',
        buttonText: 'Explore',
        buttonStyle: 'white',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuC0ehCNr5dtDmW8hw6lJ64ivQF4Fqj1OLOqRbR-9Ntpr_qt5EZJiT90LzOuUJ3xijZLq7ebsoMDwO7liSrSIHfiSpFyhTUKvUlIQ7OmwzlYIWcrftaQ1OVAieUV9skSrrPyTM7Qv3KfO6Ho9D-IR-S6gsvakW0bNwanIeOPITUmeQabf2Is7fXPYYirURuQh6I1ZDkN1uYhQ3VFNRv21hjlKvjMWJK4jWRpFAVyJsbqJ3z-KESc_XSKyrc0RYsWjR3C2vz9DOcKGvY',
    },
    {
        id: 3,
        badge: 'Smart Home',
        badgeStyle: 'primary',
        title: 'Future of Living\nStarts Here',
        subtitle: 'Upgrade your home with AI gadgets',
        buttonText: 'Shop Tech',
        buttonStyle: 'primary',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDsLJNLbDaRdlWrk30bLVXGz2zMdwABe3pWlNpNb5MV0CKXj6qLqO4WCWYwBrNCJwAF2rAqY0s_4yJ6HZ8E9hJJQ0aYQm_Xq7eJpCwQY7JOmA',
    },
    {
        id: 4,
        badge: 'Flash Sale',
        badgeStyle: 'white',
        title: 'Weekend Groceries\n50% OFF',
        subtitle: 'Fresh produce delivered to your door',
        buttonText: 'Order Now',
        buttonStyle: 'white',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCx8jrIDsQet2gsCPjmllYeBU_c-nkpb_O0bu7E910IcOGwqTbGldHyC6AhFT2sHkACQ4x4cR2T9Mh1IInp8e5aBXI7jn6E0lyyHDGwVIHStqpDWJ9OOicbmpDhOEMZh_uxumz-MzCTqI2RFfHkOzH-UGFhnEpyKptYIrKFtQYFsTO5hFeXPhU3A3irp94FpYbrwyIpg7u9PKwp_yw7f-51g1cRVM88qLCJnpMb9-0zX67a9_dQKvGbsq9BEdT6CQ_OJ9QjGyd-3GQ',
    },
]);

const startAutoplay = () => {
    stopAutoplay();
    autoplayTimer = setInterval(() => {
        if (!carouselRef.value) return;
        
        activeBannerIndex.value = (activeBannerIndex.value + 1) % banners.value.length;
        const bannerElement = carouselRef.value.children[activeBannerIndex.value] as HTMLElement;
        
        if (bannerElement) {
            bannerElement.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
            });
        }
    }, 4000);
};

const stopAutoplay = () => {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
};

onMounted(() => {
    startAutoplay();
});

onUnmounted(() => {
    stopAutoplay();
});

const products = ref<Product[]>([
    {
        id: 1,
        name: 'WH-1000XM4 Noise Canceling Wireless...',
        brand: 'Brand',
        category: 'Audio',
        price: 249.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBkdQgUIXRO2xmxQisdRa-xXNF5Xf60N8b147d5kkuAsHkR2B8sxIweopIOP4TQ-ZyafkU2CfP4ZqxpBxgSIVrL-hQXygWvNhurSpaxY1W29qELyh_NYKTClebstrSp-ZInNYMiAINV53-BKWqWJbK6YCaL1sphk566QGSEySsVrkdma9yrFh8Edcf_lzXyn431pIhFDUY8Wo7QHDbYZIIzeyKD0wibiAsBRfHXaOz5MwXWmY14QGmWO0YVxrtlKxPiUre2bKtYgks',
        isFavorite: false,
    },
    {
        id: 2,
        name: 'Series 7 GPS - 45mm Aluminum',
        brand: 'Brand',
        category: 'Wearables',
        price: 399.0,
        originalPrice: 429.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB8r0aiJ5ngCZEyqyTauHpLVAK6NNQC4pCB1S3mQJW5ceIfkdezlQeU5RZQf6CzLxQgtPxH89hDtVmPhTJxjw1CTwiThkijE1hOhLL2Uk8NWmaLTzrVNUtLdhqESUQFDYgbJKU_CLAXR_D1ml2RBVPiEmbDjDQ-J9Z6KNeBOAiG1XiEovRxgATGd5i5z2rJsbMcmkwTeBDfT4qyemWtjK46KtZbR4C3YM5yqIJDuTxlabMRGmBgxDwazyXghq9p07-kafO1Egn7o7g',
        isFavorite: false,
    },
    {
        id: 3,
        name: 'RF 50mm f/1.8 STM Prime Lens',
        brand: 'Brand',
        category: 'Photo',
        price: 199.0,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO4f9Y-jqkXBYxkKrIgQT2ozhzYGez20ZAncUOIIpa4vOjsTFuIiyrEwNPGutfh0j83ghqFinrhrmQnGRA5oZbFP9mjrC5ezTwXFcmjNukRjGMoFFf0So2n4RNdKKj1VOTroo-omFNbzEizT9CJpdt3AUErlwQ5fYyeELD2bROrjaXt4lKZhn0rGkXxwVkMXEN3GgmcWhdYvPDRTX1MY3xGH8ZOte8Kb1Xy19CFnoeQ3FRIWqQrIqv4iBCVKkQcW9kpTHQ6jXoEMw',
        isFavorite: false,
    },
    {
        id: 4,
        name: 'Modern LED Desk Lamp with USB Port',
        brand: 'Brand',
        category: 'Decor',
        price: 45.5,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCx8jrIDsQet2gsCPjmllYeBU_c-nkpb_O0bu7E910IcOGwqTbGldHyC6AhFT2sHkACQ4x4cR2T9Mh1IInp8e5aBXI7jn6E0lyyHDGwVIHStqpDWJ9OOicbmpDhOEMZh_uxumz-MzCTqI2RFfHkOzH-UGFhnEpyKptYIrKFtQYFsTO5hFeXPhU3A3irp94FpYbrwyIpg7u9PKwp_yw7f-51g1cRVM88qLCJnpMb9-0zX67a9_dQKvGbsq9BEdT6CQ_OJ9QjGyd-3GQ',
        isFavorite: true,
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
</script>

<template>
    <Head title="Compay Market" />

    <MobileLayout active-nav="home" :cart-count="cartCount">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
        <!-- Main Content -->
        <main class="flex-1 pb-24">
            <!-- Banner Carousel -->
            <div
                ref="carouselRef"
                class="hide-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 py-4 scroll-smooth"
                @touchstart="stopAutoplay"
                @touchend="startAutoplay"
            >
                <div
                    v-for="banner in banners"
                    :key="banner.id"
                    class="relative aspect-[16/9] min-w-[85%] snap-center overflow-hidden rounded-2xl bg-blue-600/10"
                >
                    <div
                        class="absolute inset-0 z-10 flex flex-col justify-center bg-gradient-to-r from-black/60 to-transparent p-6"
                    >
                        <span
                            :class="[
                                'mb-2 w-fit rounded px-2 py-1 text-[10px] font-bold uppercase tracking-wider',
                                banner.badgeStyle === 'primary'
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-black',
                            ]"
                        >
                            {{ banner.badge }}
                        </span>
                        <h2
                            class="mb-1 whitespace-pre-line text-2xl font-bold leading-tight text-white"
                        >
                            {{ banner.title }}
                        </h2>
                        <p class="mb-4 text-xs text-white/80">
                            {{ banner.subtitle }}
                        </p>
                        <button
                            :class="[
                                'w-fit rounded-lg px-4 py-2 text-sm font-bold shadow-lg',
                                banner.buttonStyle === 'primary'
                                    ? 'bg-blue-600 text-white shadow-blue-600/20'
                                    : 'bg-white text-black',
                            ]"
                        >
                            {{ banner.buttonText }}
                        </button>
                    </div>
                    <div
                        class="size-full bg-cover bg-center bg-no-repeat"
                        :style="{ backgroundImage: `url('${banner.image}')` }"
                    />
                </div>
            </div>

            <!-- Categories -->
            <div class="mb-2 mt-2 flex items-center justify-between px-4">
                <h3
                    class="text-lg font-bold leading-tight tracking-[-0.015em]"
                >
                    Categories
                </h3>
                <Link
                    href="/products"
                    class="text-xs font-bold uppercase tracking-wider text-blue-600"
                >
                    See All
                </Link>
            </div>

            <div
                class="hide-scrollbar mb-4 flex gap-6 overflow-x-auto px-4 py-2"
            >
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="`/products/${category.id}`"
                    class="flex min-w-16 flex-col items-center gap-2"
                >
                    <div
                        :class="[
                            'flex size-14 items-center justify-center rounded-full bg-white shadow-sm dark:bg-slate-800/50',
                            selectedCategory === category.id
                                ? 'border-2 border-blue-600'
                                : 'border border-transparent',
                        ]"
                    >
                        <svg
                            v-if="category.icon === 'devices'"
                            :class="[
                                'size-6',
                                selectedCategory === category.id
                                    ? 'text-blue-600'
                                    : 'text-gray-400',
                            ]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                        <svg
                            v-else-if="category.icon === 'apparel'"
                            :class="[
                                'size-6',
                                selectedCategory === category.id
                                    ? 'text-blue-600'
                                    : 'text-gray-400',
                            ]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"
                            />
                        </svg>
                        <svg
                            v-else-if="category.icon === 'home'"
                            :class="[
                                'size-6',
                                selectedCategory === category.id
                                    ? 'text-blue-600'
                                    : 'text-gray-400',
                            ]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                        <svg
                            v-else-if="category.icon === 'restaurant'"
                            :class="[
                                'size-6',
                                selectedCategory === category.id
                                    ? 'text-blue-600'
                                    : 'text-gray-400',
                            ]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>
                        <svg
                            v-else-if="category.icon === 'fitness_center'"
                            :class="[
                                'size-6',
                                selectedCategory === category.id
                                    ? 'text-blue-600'
                                    : 'text-gray-400',
                            ]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"
                            />
                        </svg>
                    </div>
                    <p
                        :class="[
                            'text-[11px] font-bold',
                            selectedCategory === category.id
                                ? 'text-blue-600'
                                : 'text-gray-500',
                        ]"
                    >
                        {{ category.name }}
                    </p>
                </Link>
            </div>

            <!-- Featured Products -->
            <div class="mb-3 mt-4 flex items-center justify-between px-4">
                <h3
                    class="text-lg font-bold leading-tight tracking-[-0.015em]"
                >
                    Featured Products
                </h3>
                <div class="flex gap-1">
                    <button class="rounded bg-black/5 p-1 dark:bg-white/5">
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
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                            />
                        </svg>
                    </button>
                    <button class="rounded p-1">
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
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 gap-4 px-4">
                <template v-if="locationMissing">
                    <ProductSkeleton v-for="n in 6" :key="n" />
                </template>
                <template v-else>
                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="flex flex-col rounded-2xl bg-white p-3 shadow-sm dark:bg-slate-800/50"
                    >
                        <div
                            class="relative mb-3 aspect-square w-full overflow-hidden rounded-xl bg-gray-50"
                        >
                            <Link :href="`/product/${product.id}`" class="block size-full">
                                <img
                                    :src="product.image"
                                    :alt="product.name"
                                    class="size-full object-cover"
                                />
                            </Link>
                            <button
                                class="absolute right-2 top-2 flex size-8 items-center justify-center rounded-full bg-white/80 backdrop-blur"
                                @click.stop="toggleFavorite(product.id)"
                            >
                                <svg
                                    :class="[
                                        'size-5',
                                        product.isFavorite
                                            ? 'fill-red-500 text-red-500'
                                            : 'text-gray-600',
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
                            <p class="mb-1 text-xs font-medium text-gray-400">
                                {{ product.brand }} &bull; {{ product.category }}
                            </p>
                            <h4 class="mb-2 line-clamp-2 text-sm font-bold leading-snug">
                                {{ product.name }}
                            </h4>
                        </Link>
                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-lg font-extrabold text-blue-600">
                                    {{ formatPrice(product.price) }}
                                </span>
                                <span
                                    v-if="product.originalPrice"
                                    class="text-[10px] text-gray-400 line-through"
                                >
                                    {{ formatPrice(product.originalPrice) }}
                                </span>
                            </div>
                            <button
                                class="flex size-8 items-center justify-center rounded-lg bg-blue-600 text-white"
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
                </template>
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
