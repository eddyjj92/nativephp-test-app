<script setup lang="ts">
import { Head, Link, usePage, Deferred } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useImageRefresh } from '@/composables/useImageRefresh';
import { useCart } from '@/composables/useCart';
import { useFavorites } from '@/composables/useFavorites';
import MobileLayout from '@/layouts/MobileLayout.vue';

const { handleImageError } = useImageRefresh();
const { addToCart: addToCartOptimistic } = useCart();
const { removeFromFavorites, clearFavorites, favoritesCount } = useFavorites();

const page = usePage();
const favoritesData = computed(() => (page.props.favorites as any) || { items: [], count: 0, ids: [] });
const favoriteItems = computed(() => favoritesData.value.items || []);

const itemCount = computed(() => favoritesData.value.count ?? 0);
const favoritesSkeletonCount = computed(() => {
    const count = Number(page.props.favoritesCount ?? itemCount.value ?? favoritesCount.value ?? 0);

    if (Number.isFinite(count) && count > 0) {
        return Math.min(count, 6);
    }

    return 0;
});

function formatPrice(price: number): string {
    const currency = page.props.selectedCurrency as any;
    const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
    return `${symbol}${price.toFixed(2)}`;
}

function getDiscountedPrice(product: any): number {
    if (!product.activeDiscounts || product.activeDiscounts.length === 0) {
        return product.salePrice;
    }

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

function hasDiscount(product: any): boolean {
    return product.activeDiscounts && product.activeDiscounts.length > 0;
}

function addToCart(product: any) {
    addToCartOptimistic(product, 1);
}
</script>

<template>
    <Head title="Saved Items" />

    <MobileLayout active-nav="saved">
        <div
            class="flex flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
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
                    v-if="favoriteItems.length > 0"
                    class="text-sm font-semibold text-primary"
                    @click="clearFavorites"
                >
                    Limpiar Todo
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
            <Deferred data="favorites">
                <template #fallback>
                    <!-- Skeleton Loading -->
                    <div
                        v-if="favoritesSkeletonCount > 0"
                        class="px-4 py-4"
                    >
                        <div class="h-4 w-48 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                    </div>
                    <div
                        v-if="favoritesSkeletonCount > 0"
                        class="flex flex-col gap-4 px-4"
                    >
                        <div
                            v-for="i in favoritesSkeletonCount"
                            :key="i"
                            class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 dark:border-white/5 dark:bg-slate-800/50"
                        >
                            <div class="size-28 shrink-0 animate-pulse rounded-xl bg-gray-200 dark:bg-slate-700" />
                            <div class="flex flex-1 flex-col gap-2">
                                <div class="h-5 w-3/4 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                                <div class="h-4 w-1/2 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                                <div class="mt-auto">
                                    <div class="h-6 w-20 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                                </div>
                                <div class="h-10 w-full animate-pulse rounded-lg bg-gray-200 dark:bg-slate-700" />
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Items Count -->
                <div v-if="favoriteItems.length > 0" class="px-4 py-4">
                    <p class="text-sm text-gray-500">
                        {{ itemCount }} producto{{ itemCount !== 1 ? 's' : '' }} guardados en su lista
                    </p>
                </div>

                <!-- Empty State -->
                <div
                    v-if="favoriteItems.length === 0"
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
                        No tienes favoritos guardados
                    </h2>
                    <p class="mb-6 text-center text-sm text-gray-400">
                        Los productos que guardes aparecerán aquí.<br />¡Explora y guarda tus favoritos!
                    </p>
                    <Link
                        href="/products"
                        class="rounded-xl bg-primary px-6 py-3 text-sm font-bold text-white"
                    >
                        Explorar Productos
                    </Link>
                </div>

                <!-- Favorites List -->
                <div v-else class="flex flex-col gap-4 px-4">
                <div
                    v-for="item in favoriteItems"
                    :key="item.product.id"
                    class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 dark:border-white/5 dark:bg-slate-800/50"
                >
                    <!-- Product Image -->
                    <Link
                        :href="`/product/${item.product.id}`"
                        class="relative size-28 shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-slate-700/50"
                    >
                        <img
                            :src="item.product.image"
                            :alt="item.product.name"
                            class="size-full object-cover"
                            @error="handleImageError"
                        />
                        <button
                            class="absolute right-2 top-2 flex size-7 items-center justify-center rounded-full bg-white/90 shadow-sm backdrop-blur"
                            @click.prevent="removeFromFavorites(item.product.id)"
                        >
                            <svg
                                class="size-4 fill-red-500 text-red-500"
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
                        <div v-if="item.product.rating" class="mb-1 flex items-center gap-1">
                            <svg
                                class="size-4 fill-amber-400 text-amber-400"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                                />
                            </svg>
                            <span class="text-xs text-gray-500">
                                {{ item.product.rating }} ({{ item.product.reviewsCount || 0 }} reviews)
                            </span>
                        </div>

                        <!-- Name -->
                        <Link :href="`/product/${item.product.id}`">
                            <h3 class="mb-1 font-bold leading-tight">{{ item.product.name }}</h3>
                        </Link>

                        <!-- Price -->
                        <div class="mb-3 flex items-baseline gap-2">
                            <span class="text-lg font-extrabold text-primary">
                                {{ formatPrice(getDiscountedPrice(item.product)) }}
                            </span>
                            <span
                                v-if="hasDiscount(item.product)"
                                class="text-sm text-gray-400 line-through"
                            >
                                {{ formatPrice(item.product.salePrice) }}
                            </span>
                        </div>

                        <!-- Add to Cart Button -->
                        <button
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-2.5 text-sm font-bold text-white dark:text-black transition-colors hover:bg-primary/90"
                            @click="addToCart(item.product)"
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
                            Agregar al Carrito
                        </button>
                    </div>
                </div>
            </div>

                <!-- Hint Text -->
                <div v-if="favoriteItems.length > 0" class="px-4 py-6 text-center">
                    <p class="text-sm italic text-gray-400">
                        Toca el corazón para eliminar un producto de tu lista.
                    </p>
                </div>
            </Deferred>
        </main>

        </div>
    </MobileLayout>
</template>
