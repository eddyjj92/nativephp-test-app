<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useImageRefresh } from '@/composables/useImageRefresh';
import MobileLayout from '@/layouts/MobileLayout.vue';
import { useCart } from '@/composables/useCart';
import type { Product } from '@/types';

const props = defineProps<{
    product: Product;
}>();

const { handleImageError } = useImageRefresh();
const page = usePage();
const quantity = ref(1);
const showFullDescription = ref(false);
const imageLoading = ref(true);

function onImageLoad() {
    imageLoading.value = false;
}

function onImageError(event: Event) {
    imageLoading.value = false;
    handleImageError(props.product.id, event);
}

const hasDiscount = computed(() => {
    return props.product.activeDiscounts && props.product.activeDiscounts.length > 0;
});

const discountedPrice = computed(() => {
    if (!hasDiscount.value) {
        return props.product.salePrice;
    }

    let price = props.product.salePrice;

    for (const discount of props.product.activeDiscounts) {
        if (discount.type === 'percentage') {
            price -= price * (discount.value / 100);
        } else {
            price -= discount.value;
        }
    }

    return Math.max(0, price);
});

const discountBadge = computed(() => {
    if (!hasDiscount.value) return null;
    const discount = props.product.activeDiscounts[0];
    const page = usePage();
    const currency = page.props.selectedCurrency as any;
    const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
    return discount.type === 'percentage' ? `-${discount.value}%` : `-${symbol}${discount.value}`;
});

const isAvailable = computed(() => {
    // If stock is null (not provided by API), assume available if status is ENABLED
    // If stock is provided, check that it's greater than 0
    return props.product.status === 'ENABLED' && (props.product.stock === null || props.product.stock > 0);
});

const stockStatus = computed(() => {
    if (props.product.stock === null) {
        return { text: 'Disponible', class: 'text-green-600' };
    }
    if (props.product.stock === 0) {
        return { text: 'Agotado', class: 'text-red-600' };
    }
    if (props.product.stock <= 5) {
        return { text: `Quedan ${props.product.stock} unidades`, class: 'text-orange-500' };
    }
    return { text: `En stock (${props.product.stock} unidades)`, class: 'text-green-600' };
});

function formatPrice(price: number): string {
    const page = usePage();
    const currency = page.props.selectedCurrency as any;
    const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
    return `${symbol}${price.toFixed(2)}`;
}

function incrementQuantity() {
    if (quantity.value < props.product.stock) {
        quantity.value++;
    }
}

function decrementQuantity() {
    if (quantity.value > 1) {
        quantity.value--;
    }
}

// Favoritos desde el backend
const favoriteIds = computed(() => {
    const favorites = page.props.favorites as any;
    return new Set(favorites?.ids?.map((id: number | string) => Number(id)) ?? []);
});

const isAnimating = ref(false);
const confettiParticles = ref<Array<{ id: number; x: number; y: number; rotation: number; scale: number; delay: number }>>([]);

function isFavorite(): boolean {
    return favoriteIds.value.has(props.product.id);
}

function generateConfetti() {
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
    confettiParticles.value = particles;
}

function toggleFavorite() {
    const willBeFavorite = !isFavorite();

    if (willBeFavorite) {
        router.post('/favorites', {
            product_id: props.product.id,
        }, {
            preserveScroll: true,
        });

        isAnimating.value = true;
        generateConfetti();

        setTimeout(() => {
            isAnimating.value = false;
        }, 600);

        setTimeout(() => {
            confettiParticles.value = [];
        }, 1000);
    } else {
        router.delete(`/favorites/${props.product.id}`, {
            preserveScroll: true,
        });
    }
}

const { addToCart: addToCartOptimistic } = useCart();

function addToCart() {
    if (!isAvailable.value) return;
    addToCartOptimistic(props.product, quantity.value);
}
</script>

<template>
    <MobileLayout active-nav="catalog">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- Header -->
            <header class="sticky top-0 z-50 bg-slate-50 py-4 dark:bg-slate-900">
                <div class="flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/products"
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </Link>
                        <h1 class="text-lg font-bold">Detalles del Producto</h1>
                    </div>
                    <button class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <!-- Product Image -->
                <div class="relative aspect-square w-full bg-white dark:bg-slate-800">
                    <!-- Skeleton loader -->
                    <div
                        v-if="imageLoading"
                        class="absolute inset-0 flex items-center justify-center"
                    >
                        <div class="size-full animate-pulse bg-slate-200 dark:bg-slate-700">
                            <div class="flex size-full items-center justify-center">
                                <div class="relative">
                                    <svg
                                        class="size-16 text-slate-300 dark:text-slate-600"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <!-- Spinner -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg
                                            class="size-8 animate-spin text-blue-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            />
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <img
                        :src="product.image"
                        :alt="product.name"
                        :class="['size-full object-contain p-4 transition-opacity duration-300', imageLoading ? 'opacity-0' : 'opacity-100']"
                        @load="onImageLoad"
                        @error="onImageError"
                    />

                    <!-- Discount Badge -->
                    <div
                        v-if="discountBadge"
                        class="absolute left-4 top-4 rounded-full bg-red-500 px-3 py-1 text-sm font-bold text-white"
                    >
                        {{ discountBadge }}
                    </div>

                    <!-- Out of Stock Overlay -->
                    <div
                        v-if="!isAvailable"
                        class="absolute inset-0 flex items-center justify-center bg-black/50"
                    >
                        <span class="rounded-full bg-white px-6 py-2 text-lg font-bold text-gray-900"> Agotado </span>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="px-4 py-6">
                    <!-- Category -->
                    <Link
                        v-if="product.category"
                        :href="`/products/${product.category.slug}`"
                        class="mb-2 inline-block text-xs font-bold uppercase tracking-wider text-primary"
                    >
                        {{ product.category.name }}
                    </Link>

                    <!-- Name & Price -->
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <h2 class="text-2xl font-bold leading-tight">{{ product.name }}</h2>
                        <div class="shrink-0 text-right">
                            <span class="block rounded-full bg-primary px-4 py-2 text-sm font-bold text-white">
                                {{ formatPrice(discountedPrice) }}
                            </span>
                            <span v-if="hasDiscount" class="mt-1 block text-sm text-gray-400 line-through">
                                {{ formatPrice(product.salePrice) }}
                            </span>
                        </div>
                    </div>

                    <!-- Product Code -->
                    <p class="mb-4 text-xs text-gray-400">Código: {{ product.code }}</p>

                    <!-- Description -->
                    <div v-if="product.description" class="mb-6">
                        <h3 class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400">Descripción</h3>
                        <p
                            :class="[
                                'text-sm leading-relaxed text-gray-600 dark:text-gray-300',
                                !showFullDescription && 'line-clamp-3',
                            ]"
                        >
                            {{ product.description }}
                        </p>
                        <button
                            v-if="product.description.length > 150"
                            class="mt-1 text-sm font-semibold text-primary"
                            @click="showFullDescription = !showFullDescription"
                        >
                            {{ showFullDescription ? 'Ver menos' : 'Leer más' }}
                        </button>
                    </div>

                    <!-- Product Details -->
                    <div class="mb-6 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Detalles</h3>

                        <div class="flex items-center justify-between rounded-lg bg-white p-3 dark:bg-slate-800/50">
                            <span class="text-sm text-gray-500">Peso</span>
                            <span class="font-medium">{{ product.weight }} kg</span>
                        </div>

                        <div
                            v-if="product.freeShipping"
                            class="flex items-center gap-2 rounded-lg bg-green-50 p-3 dark:bg-green-900/20"
                        >
                            <svg class="size-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            <span class="text-sm font-medium text-green-600">Envío gratis</span>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div v-if="isAvailable" class="mb-6">
                        <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">Cantidad</h3>
                        <div class="flex items-center justify-between">
                            <span :class="['text-sm font-medium', stockStatus.class]">
                                {{ stockStatus.text }}
                            </span>
                            <div class="flex items-center gap-3">
                                <button
                                    class="flex size-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-white/10 dark:text-white dark:hover:bg-slate-800"
                                    :disabled="quantity <= 1"
                                    @click="decrementQuantity"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    class="flex size-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-white/10 dark:text-white dark:hover:bg-slate-800"
                                    :disabled="quantity >= product.stock"
                                    @click="incrementQuantity"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                    <!-- Out of Stock Message -->
                    <div
                        v-else
                        class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-900/20"
                    >
                        <p class="text-center text-sm font-medium text-red-600">
                            Este producto no está disponible actualmente
                        </p>
                    </div>
                </div>
            </main>

            <!-- Bottom Action Bar -->
            <div
                class="fixed inset-x-0 bottom-[calc(var(--inset-bottom,0px)+3.8rem)] z-40 border-t border-gray-100 bg-white px-4 pb-4 pt-4 dark:border-white/5 dark:bg-slate-900"
            >
                <div class="flex items-center gap-3">
                    <button
                        :class="[
                            'relative flex size-14 items-center justify-center rounded-xl border transition-all',
                            isFavorite()
                                ? 'border-red-200 bg-red-50 dark:border-red-900/50 dark:bg-red-900/20'
                                : 'border-gray-200 bg-white dark:border-white/10 dark:bg-slate-800',
                            isAnimating ? 'animate-heartbeat' : '',
                        ]"
                        @click="toggleFavorite"
                    >
                        <!-- Confetti -->
                        <div v-if="confettiParticles.length > 0" class="pointer-events-none absolute inset-0 overflow-visible">
                            <span
                                v-for="particle in confettiParticles"
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
                                'size-6 transition-all duration-200',
                                isFavorite() ? 'fill-red-500 text-red-500' : 'fill-transparent text-gray-600 dark:text-gray-400',
                                isAnimating ? 'scale-125' : '',
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
                    <button
                        :disabled="!isAvailable"
                        :class="[
                            'flex flex-1 items-center justify-center gap-2 rounded-xl py-4 font-bold text-white transition-colors',
                            isAvailable
                                ? 'bg-primary shadow-lg shadow-primary/30 hover:bg-primary/90'
                                : 'cursor-not-allowed bg-gray-300',
                        ]"
                        @click="addToCart"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                            />
                        </svg>
                        {{ isAvailable ? 'Agregar al Carrito' : 'No Disponible' }}
                    </button>
                </div>
            </div>
        </div>
    </MobileLayout>
</template>

<style scoped>
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
