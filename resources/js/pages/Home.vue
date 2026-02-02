<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import CategoriesCarousel from '@/components/CategoriesCarousel.vue';
import ProductSkeleton from '@/components/ProductSkeleton.vue';
import MobileLayout from '@/layouts/MobileLayout.vue';
import { add } from '@/routes/cart';
import type { Category, Product } from '@/types';

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
    recommendedProducts: Product[];
    newArrivals: Product[];
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
        const currency = page.props.selectedCurrency as any;
        const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
        return discount.type === 'percentage' ? `${discount.value}% OFF` : `${symbol}${discount.value} OFF`;
    }
    return banner.type === 'informative' ? 'Nuevo' : '';
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
            // Use scrollLeft instead of scrollIntoView to avoid vertical page scroll
            const containerWidth = carouselRef.value.offsetWidth;
            const bannerWidth = bannerElement.offsetWidth;
            const bannerLeft = bannerElement.offsetLeft;
            const scrollPosition = bannerLeft - (containerWidth - bannerWidth) / 2;

            carouselRef.value.scrollTo({
                left: scrollPosition,
                behavior: 'smooth'
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

const cartCount = ref(3);
const favorites = ref<Set<number>>(new Set());

function toggleFavorite(productId: number) {
    if (favorites.value.has(productId)) {
        favorites.value.delete(productId);
    } else {
        favorites.value.add(productId);
    }
}

function isFavorite(productId: number): boolean {
    return favorites.value.has(productId);
}

function formatPrice(price: number): string {
    const currency = page.props.selectedCurrency as any;
    const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
    return `${symbol}${price.toFixed(2)}`;
}

function getDiscountedPrice(product: Product): number | null {
    if (product.activeDiscounts.length === 0) return null;

    let price = product.salePrice;
    for (const discount of product.activeDiscounts) {
        if (discount.type === 'percentage') {
            price = price * (1 - discount.value / 100);
        } else {
            price = price - discount.value;
        }
    }
    return Math.max(0, price);
}

function hasDiscount(product: Product): boolean {
    return product.activeDiscounts.length > 0;
}

function addToCart(product: Product) {
    router.post(add().url, {
        product_id: product.id,
        quantity: 1,
    }, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Compay Market" />

    <MobileLayout active-nav="home" :cart-count="cartCount">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
        <!-- Main Content -->
        <main class="flex-1 pb-4">
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

            <!-- Recommended Products -->
            <section v-if="recommendedProducts.length > 0 || locationMissing" class="mt-6">
                <div class="mb-3 flex items-center justify-between px-4">
                    <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">
                        Productos Recomendados
                    </h3>
                    <Link href="/products?recommended=1" class="text-sm font-medium text-blue-600">
                        Ver todos
                    </Link>
                </div>

                <div class="grid grid-cols-2 gap-4 px-4">
                    <template v-if="locationMissing">
                        <ProductSkeleton v-for="n in 4" :key="n" />
                    </template>
                    <template v-else>
                        <div
                            v-for="product in recommendedProducts"
                            :key="product.id"
                            class="flex flex-col rounded-2xl bg-white p-3 shadow-sm dark:bg-slate-800/50"
                        >
                            <div class="relative mb-3 aspect-square w-full overflow-hidden rounded-xl bg-gray-50">
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
                                            isFavorite(product.id)
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
                                <span
                                    v-if="hasDiscount(product)"
                                    class="absolute left-2 top-2 rounded bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white"
                                >
                                    -{{ product.activeDiscounts[0].value }}%
                                </span>
                            </div>
                            <Link :href="`/products/${product.slug}`">
                                <p class="mb-1 text-xs font-medium text-gray-400">
                                    {{ product.category?.name ?? 'Sin categoría' }}
                                </p>
                                <h4 class="mb-2 line-clamp-2 text-sm font-bold leading-snug">
                                    {{ product.name }}
                                </h4>
                            </Link>
                            <div class="mt-auto flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-lg font-extrabold text-blue-600">
                                        {{ formatPrice(getDiscountedPrice(product) ?? product.salePrice) }}
                                    </span>
                                    <span
                                        v-if="hasDiscount(product)"
                                        class="text-[10px] text-gray-400 line-through"
                                    >
                                        {{ formatPrice(product.salePrice) }}
                                    </span>
                                </div>
                                <button
                                    class="flex size-8 items-center justify-center rounded-lg bg-blue-600 text-white"
                                    @click="addToCart(product)"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- New Arrivals -->
            <section v-if="newArrivals.length > 0 || locationMissing" class="mt-6">
                <div class="mb-3 flex items-center justify-between px-4">
                    <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">
                        Nuevos Arribos
                    </h3>
                    <Link href="/products?new=1" class="text-sm font-medium text-blue-600">
                        Ver todos
                    </Link>
                </div>

                <div class="grid grid-cols-2 gap-4 px-4">
                    <template v-if="locationMissing">
                        <ProductSkeleton v-for="n in 4" :key="n" />
                    </template>
                    <template v-else>
                        <div
                            v-for="product in newArrivals"
                            :key="product.id"
                            class="flex flex-col rounded-2xl bg-white p-3 shadow-sm dark:bg-slate-800/50"
                        >
                            <div class="relative mb-3 aspect-square w-full overflow-hidden rounded-xl bg-gray-50">
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
                                            isFavorite(product.id)
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
                                <span class="absolute left-2 top-2 rounded bg-green-500 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                    Nuevo
                                </span>
                            </div>
                            <Link :href="`/products/${product.slug}`">
                                <p class="mb-1 text-xs font-medium text-gray-400">
                                    {{ product.category?.name ?? 'Sin categoría' }}
                                </p>
                                <h4 class="mb-2 line-clamp-2 text-sm font-bold leading-snug">
                                    {{ product.name }}
                                </h4>
                            </Link>
                            <div class="mt-auto flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-lg font-extrabold text-blue-600">
                                        {{ formatPrice(getDiscountedPrice(product) ?? product.salePrice) }}
                                    </span>
                                    <span
                                        v-if="hasDiscount(product)"
                                        class="text-[10px] text-gray-400 line-through"
                                    >
                                        {{ formatPrice(product.salePrice) }}
                                    </span>
                                </div>
                                <button
                                    class="flex size-8 items-center justify-center rounded-lg bg-blue-600 text-white"
                                    @click="addToCart(product)"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
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
