<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useImageRefresh } from '@/composables/useImageRefresh';

const emit = defineEmits(['close']);

const { handleImageError } = useImageRefresh();
const page = usePage();

const searchQuery = ref('');
const searchInput = ref<HTMLInputElement | null>(null);
const products = ref<any[]>([]);
const isLoading = ref(false);
const hasSearched = ref(false);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

function formatPrice(price: number): string {
    const currency = page.props.selectedCurrency as any;
    const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
    return `${symbol}${price.toFixed(2)}`;
}

function getDiscountedPrice(product: any): number {
    if (!product.activeDiscounts || product.activeDiscounts.length === 0) {
        return product.salePrice;
    }
    const discount = product.activeDiscounts[0];
    if (discount.type === 'percentage') {
        return product.salePrice * (1 - discount.value / 100);
    }
    return product.salePrice - discount.value;
}

function hasDiscount(product: any): boolean {
    return product.activeDiscounts && product.activeDiscounts.length > 0;
}

async function searchProducts(query: string) {
    if (query.length < 2) {
        products.value = [];
        hasSearched.value = false;
        return;
    }

    isLoading.value = true;
    hasSearched.value = true;

    try {
        const response = await fetch(`/products/search?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        products.value = data.products || [];
    } catch (error) {
        console.error('Error searching products:', error);
        products.value = [];
    } finally {
        isLoading.value = false;
    }
}

watch(searchQuery, (newQuery) => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
    debounceTimer = setTimeout(() => {
        searchProducts(newQuery);
    }, 300);
});

nextTick(() => {
    searchInput.value?.focus();
});

function close() {
    emit('close');
}

function goToProduct(productId: number) {
    close();
}
</script>

<template>
    <div
        class="fixed inset-0 z-[100] flex flex-col bg-slate-50 dark:bg-slate-900"
        style="padding-top: var(--inset-top, 0px); padding-bottom: var(--inset-bottom, 0px);"
    >
        <!-- Search Header -->
        <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-3 dark:border-white/5">
            <button
                class="flex size-10 shrink-0 items-center justify-center rounded-full text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                @click="close"
            >
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <div class="flex flex-1 items-center rounded-xl border border-gray-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-800">
                <svg class="mr-3 size-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    ref="searchInput"
                    v-model="searchQuery"
                    type="text"
                    placeholder="Buscar productos..."
                    class="flex-1 border-none bg-transparent text-base font-medium placeholder-gray-400 focus:outline-none focus:ring-0 dark:text-white"
                />
                <button
                    v-if="searchQuery"
                    class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    @click="searchQuery = ''"
                >
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Results -->
        <div class="flex-1 overflow-y-auto">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex flex-col gap-4 p-4">
                <div v-for="i in 4" :key="i" class="flex gap-4">
                    <div class="size-20 shrink-0 animate-pulse rounded-xl bg-gray-200 dark:bg-slate-700" />
                    <div class="flex flex-1 flex-col gap-2">
                        <div class="h-5 w-3/4 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                        <div class="h-4 w-1/2 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                        <div class="h-5 w-20 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                    </div>
                </div>
            </div>

            <!-- Empty State - Before Search -->
            <div v-else-if="!hasSearched" class="flex flex-col items-center justify-center px-4 py-16 text-center">
                <svg class="mb-4 size-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 class="mb-2 text-lg font-bold text-gray-600 dark:text-gray-400">Buscar productos</h3>
                <p class="text-sm text-gray-400">Escribe al menos 2 caracteres para buscar</p>
            </div>

            <!-- No Results -->
            <div v-else-if="products.length === 0" class="flex flex-col items-center justify-center px-4 py-16 text-center">
                <svg class="mb-4 size-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mb-2 text-lg font-bold text-gray-600 dark:text-gray-400">Sin resultados</h3>
                <p class="text-sm text-gray-400">No encontramos productos para "{{ searchQuery }}"</p>
            </div>

            <!-- Products List -->
            <div v-else class="flex flex-col divide-y divide-gray-100 dark:divide-white/5">
                <Link
                    v-for="product in products"
                    :key="product.id"
                    :href="`/product/${product.id}`"
                    class="flex gap-4 px-4 py-3 transition-colors hover:bg-gray-50 active:bg-gray-100 dark:hover:bg-slate-800/50 dark:active:bg-slate-800"
                    @click="goToProduct(product.id)"
                >
                    <div class="size-20 shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-slate-700/50">
                        <img
                            :src="product.image"
                            :alt="product.name"
                            class="size-full object-cover"
                            @error="handleImageError($event, product.id)"
                        />
                    </div>
                    <div class="flex flex-1 flex-col justify-center">
                        <h3 class="line-clamp-2 text-sm font-bold text-slate-800 dark:text-white">
                            {{ product.name }}
                        </h3>
                        <p v-if="product.category?.name" class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ product.category.name }}
                        </p>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="text-base font-bold text-primary">
                                {{ formatPrice(getDiscountedPrice(product)) }}
                            </span>
                            <span
                                v-if="hasDiscount(product)"
                                class="text-sm text-gray-400 line-through"
                            >
                                {{ formatPrice(product.salePrice) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <svg class="size-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>
