<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';
import ProductSkeleton from '@/components/ProductSkeleton.vue';
import CategoriesCarousel from '@/components/CategoriesCarousel.vue';
import type { Category } from '@/types';

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

type Discount = {
    id: number;
    name: string;
    slug: string;
    description: string;
    type: string;
    value: number;
    applicableTo: string;
};

type InformativeBanner = {
    id: number;
    name: string;
    slug: string;
    description: string;
};

type Banner = {
    id: number;
    image: string;
    mobileImage: string;
    status: string;
    type: 'discount' | 'informative';
    bannerableId: number;
    bannerableType: string;
    bannerable: Discount | InformativeBanner | null;
};

const props = defineProps<{
    banners: Banner[];
    categories: Category[];
    categoriesNextPageUrl?: string | null;
}>();

const page = usePage();
const locationMissing = computed(() => !!page.props.showLocationModal);

const carouselRef = ref<HTMLElement | null>(null);
const activeBannerIndex = ref(0);
let autoplayTimer: ReturnType<typeof setInterval> | null = null;

function getBannerImage(banner: Banner): string {
    return banner.mobileImage || banner.image;
}

function getBannerTitle(banner: Banner): string {
    return banner.bannerable?.name ?? '';
}

function getBannerDescription(banner: Banner): string {
    return banner.bannerable?.description ?? '';
}

function getBannerBadge(banner: Banner): string {
    if (banner.type === 'discount' && banner.bannerable) {
        const discount = banner.bannerable as Discount;
        return discount.type === 'percentage' ? `${discount.value}% OFF` : `$${discount.value} OFF`;
    }
    return banner.type === 'informative' ? 'New' : '';
}

function getBannerLink(banner: Banner): string {
    if (banner.type === 'discount') {
        return `/promotions/${banner.bannerable?.slug ?? ''}`;
    }
    return `/info/${banner.bannerable?.slug ?? ''}`;
}

const startAutoplay = () => {
    stopAutoplay();
    if (props.banners.length === 0) return;

    autoplayTimer = setInterval(() => {
        if (!carouselRef.value) return;

        activeBannerIndex.value = (activeBannerIndex.value + 1) % props.banners.length;
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
                <Link
                    v-for="banner in props.banners"
                    :key="banner.id"
                    :href="getBannerLink(banner)"
                    class="relative aspect-[19/20] min-w-[85%] snap-center overflow-hidden rounded-2xl bg-blue-600/10"
                >
                    <div
                        class="absolute inset-0 z-10 flex flex-col justify-end bg-gradient-to-t from-black/70 via-black/20 to-transparent p-4"
                    >
                        <span
                            v-if="getBannerBadge(banner)"
                            :class="[
                                'mb-2 w-fit rounded px-2 py-1 text-[10px] font-bold uppercase tracking-wider',
                                banner.type === 'discount'
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-black',
                            ]"
                        >
                            {{ getBannerBadge(banner) }}
                        </span>
                        <h2
                            v-if="getBannerTitle(banner)"
                            class="mb-1 line-clamp-2 text-lg font-bold leading-tight text-white"
                        >
                            {{ getBannerTitle(banner) }}
                        </h2>
                        <p
                            v-if="getBannerDescription(banner)"
                            class="line-clamp-2 text-xs text-white/80"
                        >
                            {{ getBannerDescription(banner) }}
                        </p>
                    </div>
                    <img
                        :src="getBannerImage(banner)"
                        :alt="getBannerTitle(banner)"
                        class="size-full object-cover"
                    />
                </Link>
            </div>

            <!-- Categories -->
            <CategoriesCarousel
                :categories="categories"
                :next-page-url="categoriesNextPageUrl"
            />

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
