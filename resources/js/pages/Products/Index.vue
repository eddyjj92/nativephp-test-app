<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onUnmounted, computed } from 'vue';
import ProductSkeleton from '@/components/ProductSkeleton.vue';
import { useImageRefresh } from '@/composables/useImageRefresh';
import MobileLayout from '@/layouts/MobileLayout.vue';
import { add } from '@/routes/cart';
import type { Product } from '@/types';

type FilterOption = {
    id: string;
    label: string;
    hasDropdown: boolean;
};

const props = defineProps<{
    products: Product[];
    productsNextPageUrl?: string | null;
    currentPage: number;
    lastPage: number;
    total: number;
    categoryId?: number | null;
}>();

const categoryTitle = computed(() => {
    return 'Productos';
});

const filters = ref<FilterOption[]>([
    { id: 'filter', label: 'Filtrar', hasDropdown: false },
    { id: 'sort', label: 'Ordenar', hasDropdown: true },
    { id: 'price', label: 'Precio', hasDropdown: true },
]);

const { handleImageError } = useImageRefresh();
const page = usePage();
const isLoading = ref(false);
const triggerRef = ref<HTMLElement | null>(null);
let observer: IntersectionObserver | null = null;

function loadMore() {
    if (!props.productsNextPageUrl || isLoading.value) return;

    isLoading.value = true;

    router.get(
        props.productsNextPageUrl,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'productsNextPageUrl', 'currentPage'],
            onFinish: () => {
                isLoading.value = false;
            },
        }
    );
}

// Set up intersection observer for infinite scroll trigger
watch(
    triggerRef,
    (el, _, onCleanup) => {
        if (observer) {
            observer.disconnect();
            observer = null;
        }

        if (!el) return;

        observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting) {
                    loadMore();
                }
            },
            { threshold: 0.1, rootMargin: '400px' }
        );

        observer.observe(el);

        onCleanup(() => {
            if (observer) {
                observer.disconnect();
                observer = null;
            }
        });
    },
    { immediate: true }
);

onUnmounted(() => {
    if (observer) {
        observer.disconnect();
        observer = null;
    }
});

function formatPrice(price: number): string {
    const currency = page.props.selectedCurrency as any;
    const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
    return `${symbol}${price.toFixed(2)}`;
}

function getDiscountedPrice(product: Product): number {
    if (!product.activeDiscounts || product.activeDiscounts.length === 0) {
        return product.salePrice;
    }

    let price = product.salePrice;

    for (const discount of product.activeDiscounts) {
        if (discount.type === 'percentage') {
            price -= price * (discount.value / 100);
        } else {
            price -= discount.value;
        }
    }

    return Math.max(0, price);
}

function hasDiscount(product: Product): boolean {
    return product.activeDiscounts && product.activeDiscounts.length > 0;
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
    <Head :title="categoryTitle" />

    <MobileLayout active-nav="catalog">
        <div
            class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- Header -->
            <header class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/"
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
                        <h1 class="text-lg font-bold">{{ categoryTitle }}ssssssss</h1>
                        <span class="text-sm text-gray-500">({{ total }})</span>
                    </div>
                    <button class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+100px)]">
                <!-- Empty State -->
                <div v-if="products.length === 0 && !isLoading" class="flex flex-col items-center justify-center py-16">
                    <svg class="mb-4 size-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                        />
                    </svg>
                    <p class="text-lg font-medium text-gray-500">No hay productos disponibles</p>
                    <p class="text-sm text-gray-400">Intenta con otra categoría</p>
                </div>

                <!-- Products Grid -->
                <div v-else class="grid grid-cols-2 gap-4 py-4">
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
                                    @error="handleImageError(product.id, $event)"
                                />
                            </Link>

                            <!-- Discount Badge -->
                            <div
                                v-if="hasDiscount(product)"
                                class="absolute left-2 top-2 rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white"
                            >
                                {{
                                    product.activeDiscounts[0].type === 'percentage'
                                        ? `-${product.activeDiscounts[0].value}%`
                                        : `-${(usePage().props.selectedCurrency as any)?.isoCode === 'EUR' ? '€' : '$'}${product.activeDiscounts[0].value}`
                                }}
                            </div>

                            <!-- Out of Stock Badge -->
                            <div
                                v-if="product.stock === 0"
                                class="absolute inset-0 flex items-center justify-center bg-black/50"
                            >
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-gray-900">
                                    Agotado
                                </span>
                            </div>
                        </div>

                        <Link :href="`/product/${product.slug}`">
                            <p
                                v-if="product.category"
                                class="mb-1 text-[10px] font-medium uppercase tracking-wider text-gray-400"
                            >
                                {{ product.category.name }}
                            </p>
                            <h4 class="mb-2 line-clamp-2 text-sm font-bold leading-snug">
                                {{ product.name }}
                            </h4>
                        </Link>

                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-lg font-extrabold text-blue-600">
                                    {{ formatPrice(getDiscountedPrice(product)) }}
                                </span>
                                <span
                                    v-if="hasDiscount(product)"
                                    class="text-[10px] text-gray-400 line-through"
                                >
                                    {{ formatPrice(product.salePrice) }}
                                </span>
                            </div>
                            <button
                                :disabled="product.stock === 0"
                                :class="[
                                    'flex size-8 items-center justify-center rounded-lg text-white transition-colors',
                                    product.stock === 0
                                        ? 'cursor-not-allowed bg-gray-300'
                                        : 'bg-blue-600 hover:bg-blue-700',
                                ]"
                                @click="addToCart(product)"
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

                    <!-- Loading Skeletons -->
                    <template v-if="isLoading">
                        <ProductSkeleton v-for="n in 4" :key="`skeleton-${n}`" />
                    </template>
                </div>

                <!-- Infinite Scroll Trigger -->
                <div
                    v-if="productsNextPageUrl"
                    ref="triggerRef"
                    class="flex items-center justify-center py-4"
                >
                    <div v-if="isLoading" class="flex items-center gap-1">
                        <span class="size-2 animate-bounce rounded-full bg-blue-600 [animation-delay:-0.3s]"></span>
                        <span class="size-2 animate-bounce rounded-full bg-blue-600 [animation-delay:-0.15s]"></span>
                        <span class="size-2 animate-bounce rounded-full bg-blue-600"></span>
                    </div>
                </div>

                <!-- End of List -->
                <div
                    v-else-if="products.length > 0"
                    class="flex items-center justify-center py-4 text-sm text-gray-400"
                >
                    Has llegado al final
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
