<script setup lang="ts">
import { Head, Link, usePage, router, Deferred } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import CategoriesCarousel from '@/components/CategoriesCarousel.vue';
import ProductSkeleton from '@/components/ProductSkeleton.vue';
import { useImageRefresh } from '@/composables/useImageRefresh';
import { useCart } from '@/composables/useCart';
import { useFavorites } from '@/composables/useFavorites';
import MobileLayout from '@/layouts/MobileLayout.vue';
import type { Category, Product, Banner } from '@/types';

const props = defineProps<{
    banners?: Banner[];
    categories?: Category[];
    categoriesNextPageUrl?: string | null;
    recommendedProducts?: Product[];
    newArrivals?: Product[];
}>();

const { handleImageError } = useImageRefresh();
const { addToCart } = useCart();
const { isFavorite, toggleFavorite: toggleFavoriteOptimistic } = useFavorites();

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
        const discount = banner.bannerable;
        if (!discount.type || discount.value === undefined) return '';
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
    if (!props.banners || props.banners?.length === 0) return;

    autoplayTimer = setInterval(() => {
        if (!carouselRef.value || !props.banners || props.banners?.length === 0) return;

        activeBannerIndex.value = (activeBannerIndex.value + 1) % props.banners?.length;
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

watch(() => props.banners, () => {
    startAutoplay();
});

onMounted(() => {
    startAutoplay();
});

onUnmounted(() => {
    stopAutoplay();
});

// Estado de animación por producto
const animatingFavorites = ref<Set<number>>(new Set());
const confettiProducts = ref<Map<number, Array<{ id: number; x: number; y: number; rotation: number; scale: number; delay: number }>>>(new Map());

function isAnimating(productId: number): boolean {
    return animatingFavorites.value.has(productId);
}

function getConfetti(productId: number) {
    return confettiProducts.value.get(productId) ?? [];
}

function generateConfetti(productId: number) {
    const particles = [];
    for (let i = 0; i < 12; i++) {
        particles.push({
            id: i,
            x: (Math.random() - 0.5) * 80,
            y: (Math.random() - 0.5) * 80,
            rotation: Math.random() * 360,
            scale: 0.5 + Math.random() * 0.5,
            delay: Math.random() * 0.1,
        });
    }
    confettiProducts.value.set(productId, particles);
}

function toggleFavorite(productId: number, event: Event) {
    event.preventDefault();
    event.stopPropagation();

    const willBeFavorite = toggleFavoriteOptimistic(productId);

    if (willBeFavorite) {
        // Animación
        animatingFavorites.value.add(productId);
        generateConfetti(productId);

        setTimeout(() => {
            animatingFavorites.value.delete(productId);
        }, 600);

        setTimeout(() => {
            confettiProducts.value.delete(productId);
        }, 1000);
    }
}

function formatPrice(price: number): string {
    const currency = page.props.selectedCurrency as any;
    const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
    return `${symbol}${price.toFixed(2)}`;
}

function getDiscountedPrice(product: Product): number | null {
    if (!product || !product.activeDiscounts || product.activeDiscounts.length === 0) return null;

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
    return !!product && !!product.activeDiscounts && product.activeDiscounts.length > 0;
}

</script>

<template>
    <Head title="Compay Market" />

    <MobileLayout active-nav="home">
        <div
            class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
        <!-- Main Content -->
        <main class="lex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+0px)]">
            <!-- Banner Carousel -->
            <Deferred data="banners">
                <template #fallback>
                    <div class="flex gap-4 overflow-hidden px-4 pt-2 pb-4">
                        <div class="relative aspect-[19/20] min-w-[85%] animate-pulse rounded-2xl bg-slate-200 dark:bg-slate-700" />
                        <div class="relative aspect-[19/20] min-w-[85%] animate-pulse rounded-2xl bg-slate-200 dark:bg-slate-700" />
                    </div>
                </template>

                <div
                    ref="carouselRef"
                    class="hide-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pt-2 pb-4 scroll-smooth"
                    @touchstart="stopAutoplay"
                    @touchend="startAutoplay"
                >
                    <Link
                        v-for="banner in props.banners"
                        :key="banner.id"
                        :href="getBannerLink(banner)"
                        class="relative aspect-[19/20] min-w-[85%] snap-center overflow-hidden rounded-2xl bg-primary/10"
                    >
                        <div
                            class="absolute inset-0 z-10 flex flex-col justify-end bg-gradient-to-t from-black/70 via-black/20 to-transparent p-4"
                        >
                            <span
                                v-if="getBannerBadge(banner)"
                                :class="[
                                    'mb-2 w-fit rounded px-2 py-1 text-[10px] font-bold uppercase tracking-wider',
                                    banner.type === 'discount'
                                        ? 'bg-primary text-white'
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
            </Deferred>

            <!-- Categories -->
            <Deferred :data="['categories', 'categoriesNextPageUrl']">
                <template #fallback>
                    <div class="mb-2 mt-2 flex items-center justify-between px-4">
                        <div class="h-6 w-24 animate-pulse rounded bg-slate-200 dark:bg-slate-700"></div>
                        <div class="h-4 w-14 animate-pulse rounded bg-slate-200 dark:bg-slate-700"></div>
                    </div>
                    <div class="hide-scrollbar mb-4 flex gap-4 overflow-x-auto px-4 py-2">
                        <div v-for="n in 5" :key="n" class="flex min-w-[70px] flex-col items-center gap-2">
                            <div class="size-16 animate-pulse rounded-full bg-slate-200 dark:bg-slate-700"></div>
                            <div class="h-2 w-12 animate-pulse rounded bg-slate-200 dark:bg-slate-700"></div>
                        </div>
                    </div>
                </template>

                <CategoriesCarousel
                    :categories="props.categories ?? []"
                    :next-page-url="props.categoriesNextPageUrl"
                />
            </Deferred>

            <!-- Recommended Products -->
            <section class="mt-6">
                <div class="mb-3 flex items-center justify-between px-4">
                    <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">
                        Productos Recomendados
                    </h3>
                    <Link href="/products?recommended=1" class="text-sm font-medium text-primary">
                        Ver todos
                    </Link>
                </div>

                <Deferred data="recommendedProducts">
                    <template #fallback>
                        <div class="grid grid-cols-2 gap-4 px-4">
                            <ProductSkeleton v-for="n in 4" :key="n" />
                        </div>
                    </template>

                    <div v-if="props.recommendedProducts && props.recommendedProducts?.length > 0" class="grid grid-cols-2 gap-4 px-4">
                                                    <div
                                                        v-for="product in props.recommendedProducts"
                                                        :key="product?.id"
                                                        class="flex flex-col rounded-2xl bg-white p-3 shadow-sm dark:bg-slate-800/50"
                                                    >
                                                    <template v-if="product">
                                                        <div class="relative mb-3 aspect-square w-full overflow-hidden rounded-xl bg-gray-50">
                                                            <Link :href="`/product/${product.id}`" class="block size-full">
                                                                <img
                                                                    :src="product.image"
                                                                    :alt="product.name"
                                                                    class="size-full object-cover"
                                                                    @error="handleImageError(product.id, $event)"
                                                                />
                                                            </Link>
                                                            <button
                                                                :class="[
                                                                    'absolute right-2 top-2 z-10 flex size-8 items-center justify-center rounded-full bg-white/90 shadow-md backdrop-blur transition-transform',
                                                                    isAnimating(product.id) ? 'animate-heartbeat' : '',
                                                                ]"
                                                                @click="toggleFavorite(product.id, $event)"
                                                            >
                                                                <!-- Confetti -->
                                                                <div v-if="getConfetti(product.id).length > 0" class="pointer-events-none absolute inset-0 overflow-visible">
                                                                    <span
                                                                        v-for="particle in getConfetti(product.id)"
                                                                        :key="particle.id"
                                                                        class="absolute left-1/2 top-1/2 size-2 animate-confetti-burst rounded-full"
                                                                        :style="{
                                                                            '--confetti-x': `${particle.x}px`,
                                                                            '--confetti-y': `${particle.y}px`,
                                                                            '--confetti-rotation': `${particle.rotation}deg`,
                                                                            '--confetti-scale': particle.scale,
                                                                            '--confetti-delay': `${particle.delay}s`,
                                                                            backgroundColor: ['#ef4444', '#dc2626', '#f87171', '#fca5a5', '#ff6b6b', '#ee5a5a'][particle.id % 6],
                                                                        }"
                                                                    />
                                                                </div>
                                                                <svg
                                                                    :class="[
                                                                        'size-5 transition-all duration-200',
                                                                        isFavorite(product.id)
                                                                            ? 'fill-red-500 text-red-500'
                                                                            : 'fill-transparent text-gray-600',
                                                                        isAnimating(product.id) ? 'scale-125' : '',
                                                                    ]"
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
                                                            <span
                                                                v-if="hasDiscount(product) && product.activeDiscounts?.[0]"
                                                                class="absolute left-2 top-2 rounded bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white"
                                                            >
                                                                -{{ product.activeDiscounts[0].value }}%
                                                            </span>
                                                        </div>
                                                        <Link :href="`/product/${product.id}`">
                                                            <p class="mb-1 text-xs font-medium text-gray-400">
                                                                {{ product.category?.name ?? 'Sin categoría' }}
                                                            </p>
                                                            <h4 class="mb-2 line-clamp-2 text-sm font-bold leading-snug">
                                                                {{ product.name }}
                                                            </h4>
                                                        </Link>
                                                        <div class="mt-auto flex items-center justify-between">
                                                            <div class="flex flex-col">
                                                                <span class="text-lg font-extrabold text-primary">
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
                                                                class="flex size-8 items-center justify-center rounded-lg bg-primary text-white"
                                                                @click="addToCart(product)"
                                                            >
                                                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </Deferred>
                                    </section>
                        
                                    <!-- New Arrivals -->
                                    <Deferred data="newArrivals">
                                        <template #fallback>
                                            <section class="mt-6">
                                                <div class="mb-3 flex items-center justify-between px-4">
                                                    <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">
                                                        Nuevos Arribos
                                                    </h3>
                                                    <div class="h-4 w-16 animate-pulse rounded bg-slate-200 dark:bg-slate-700"></div>
                                                </div>
                        
                                                <div class="grid grid-cols-2 gap-4 px-4">
                                                    <ProductSkeleton v-for="n in 4" :key="n" />
                                                </div>
                                            </section>
                                        </template>
                        
                                        <section v-if="(props.newArrivals && props.newArrivals.length > 0) || locationMissing" class="mt-6">
                                            <div class="mb-3 flex items-center justify-between px-4">
                                                <h3 class="text-lg font-bold leading-tight tracking-[-0.015em]">
                                                    Nuevos Arribos
                                                </h3>
                                                <Link href="/products?new=1" class="text-sm font-medium text-primary">
                                                    Ver todos
                                                </Link>
                                            </div>
                        
                                            <div class="grid grid-cols-2 gap-4 px-4">
                                                <template v-if="locationMissing">
                                                    <ProductSkeleton v-for="n in 4" :key="n" />
                                                </template>
                                                <template v-else-if="props.newArrivals">
                                                    <div
                                                        v-for="product in props.newArrivals"
                                                        :key="product?.id"
                                                        class="flex flex-col rounded-2xl bg-white p-3 shadow-sm dark:bg-slate-800/50"
                                                    >
                                                    <template v-if="product">
                                                        <div class="relative mb-3 aspect-square w-full overflow-hidden rounded-xl bg-gray-50">
                                                            <Link :href="`/product/${product.id}`" class="block size-full">
                                                                <img
                                                                    :src="product.image"
                                                                    :alt="product.name"
                                                                    class="size-full object-cover"
                                                                    @error="handleImageError(product.id, $event)"
                                                                />
                                                            </Link>
                                                            <button
                                                                :class="[
                                                                    'absolute right-2 top-2 z-10 flex size-8 items-center justify-center rounded-full bg-white/90 shadow-md backdrop-blur transition-transform',
                                                                    isAnimating(product.id) ? 'animate-heartbeat' : '',
                                                                ]"
                                                                @click="toggleFavorite(product.id, $event)"
                                                            >
                                                                <!-- Confetti -->
                                                                <div v-if="getConfetti(product.id).length > 0" class="pointer-events-none absolute inset-0 overflow-visible">
                                                                    <span
                                                                        v-for="particle in getConfetti(product.id)"
                                                                        :key="particle.id"
                                                                        class="absolute left-1/2 top-1/2 size-2 animate-confetti-burst rounded-full"
                                                                        :style="{
                                                                            '--confetti-x': `${particle.x}px`,
                                                                            '--confetti-y': `${particle.y}px`,
                                                                            '--confetti-rotation': `${particle.rotation}deg`,
                                                                            '--confetti-scale': particle.scale,
                                                                            '--confetti-delay': `${particle.delay}s`,
                                                                            backgroundColor: ['#ef4444', '#dc2626', '#f87171', '#fca5a5', '#ff6b6b', '#ee5a5a'][particle.id % 6],
                                                                        }"
                                                                    />
                                                                </div>
                                                                <svg
                                                                    :class="[
                                                                        'size-5 transition-all duration-200',
                                                                        isFavorite(product.id)
                                                                            ? 'fill-red-500 text-red-500'
                                                                            : 'fill-transparent text-gray-600',
                                                                        isAnimating(product.id) ? 'scale-125' : '',
                                                                    ]"
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
                                                            <span class="absolute left-2 top-2 rounded bg-secondary px-1.5 py-0.5 text-[10px] font-bold text-white">
                                                                Nuevo
                                                            </span>
                                                        </div>
                                                        <Link :href="`/product/${product.id}`">
                                                            <p class="mb-1 text-xs font-medium text-gray-400">
                                                                {{ product.category?.name ?? 'Sin categoría' }}
                                                            </p>
                                                            <h4 class="mb-2 line-clamp-2 text-sm font-bold leading-snug">
                                                                {{ product.name }}
                                                            </h4>
                                                        </Link>
                                                        <div class="mt-auto flex items-center justify-between">
                                                            <div class="flex flex-col">
                                                                <span class="text-lg font-extrabold text-primary">
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
                                                                class="flex size-8 items-center justify-center rounded-lg bg-primary text-white"
                                                                @click="addToCart(product)"
                                                            >
                                                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </section>            </Deferred>
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

@keyframes heartbeat {
    0% { transform: scale(1); }
    15% { transform: scale(1.3); }
    30% { transform: scale(1); }
    45% { transform: scale(1.2); }
    60% { transform: scale(1); }
}

@keyframes confetti-burst {
    0% {
        transform: translate(-50%, -50%) translate(0, 0) rotate(0deg) scale(0);
        opacity: 1;
    }
    20% {
        transform: translate(-50%, -50%) translate(calc(var(--confetti-x) * 0.3), calc(var(--confetti-y) * 0.3)) rotate(calc(var(--confetti-rotation) * 0.3)) scale(var(--confetti-scale));
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) translate(var(--confetti-x), var(--confetti-y)) rotate(var(--confetti-rotation)) scale(0);
        opacity: 0;
    }
}

.animate-heartbeat {
    animation: heartbeat 0.6s ease-in-out;
}

.animate-confetti-burst {
    animation: confetti-burst 0.8s ease-out forwards;
    animation-delay: var(--confetti-delay);
}
</style>
