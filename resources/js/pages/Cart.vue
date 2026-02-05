<script setup lang="ts">
import { Head, Link, usePage, router, Deferred } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useImageRefresh } from '@/composables/useImageRefresh';
import { useCart } from '@/composables/useCart';
import LoginModal from '@/components/LoginModal.vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

const { handleImageError } = useImageRefresh();
const { isProcessing, incrementQuantity, decrementQuantity, removeFromCart, clearCart } = useCart();

const page = usePage();
const cart = computed(() => (page.props.cart as any) || { items: [], count: 0, total: 0 });
const cartItems = computed(() => cart.value.items || []);

const showLoginModal = ref(false);
const isAuthenticated = computed(() => !!(page.props.auth as any)?.user);

const total = computed(() => {
    return cart.value.total;
});

const originalCartSubtotal = computed(() => {
    return cartItems.value.reduce((sum: number, item: any) => {
        return sum + (item.product.salePrice * item.quantity);
    }, 0);
});

const cartHasDiscount = computed(() => {
    return originalCartSubtotal.value > total.value;
});

function formatPrice(price: number): string {
    const currency = page.props.selectedCurrency as any;
    const symbol = currency?.isoCode === 'EUR' ? '€' : '$';
    return `${symbol}${price.toFixed(2)}`;
}

function getDiscountPercentage(product: any): number | null {
    if (!product?.activeDiscounts || product.activeDiscounts.length === 0) {
        return null;
    }

    const percentageDiscount = product.activeDiscounts.find(
        (discount: any) => discount.type === 'percentage' && discount.value > 0,
    );

    return percentageDiscount ? percentageDiscount.value : null;
}

function hasActiveDiscount(product: any): boolean {
    return !!product?.activeDiscounts?.length;
}

function goToCheckout() {
    if (isAuthenticated.value) {
        router.visit('/checkout');
    } else {
        showLoginModal.value = true;
    }
}
</script>

<template>
    <Head title="Shopping Cart" />

    <MobileLayout active-nav="cart">
        <div
            class="flex flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
        <!-- Header -->
        <header
            class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900"
        >
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
                    <h1 class="text-lg font-bold">Carrito de Compras</h1>
                </div>
                <button
                    v-if="cartItems.length > 0"
                    class="text-sm font-semibold text-primary"
                    @click="clearCart"
                >
                    Limpiar Todo
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
            <Deferred data="cart">
                <template #fallback>
                    <!-- Skeleton Loading -->
                    <div class="divide-y divide-gray-100 dark:divide-white/5">
                        <div
                            v-for="i in 2"
                            :key="i"
                            class="flex gap-4 bg-white px-4 py-4 dark:bg-slate-800/30"
                        >
                            <div class="size-24 shrink-0 animate-pulse rounded-xl bg-gray-200 dark:bg-slate-700" />
                            <div class="flex flex-1 flex-col gap-2">
                                <div class="h-5 w-3/4 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                                <div class="h-4 w-1/2 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                                <div class="mt-auto flex items-center justify-between">
                                    <div class="h-8 w-24 animate-pulse rounded-lg bg-gray-200 dark:bg-slate-700" />
                                    <div class="h-6 w-16 animate-pulse rounded bg-gray-200 dark:bg-slate-700" />
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty Cart State -->
                <div
                    v-if="cartItems.length === 0"
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
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                        />
                    </svg>
                    <h2 class="mb-2 text-lg font-bold text-gray-600 dark:text-gray-400">
                        Tu carrito está vacío
                    </h2>
                    <p class="mb-6 text-sm text-gray-400">
                        Parece que no has agregado nada aún
                    </p>
                    <Link
                        href="/products"
                        class="rounded-xl bg-primary px-6 py-3 text-sm font-bold text-white"
                    >
                        Empezar a Comprar
                    </Link>
                </div>

                <!-- Cart Items -->
                <div v-else class="divide-y divide-gray-100 dark:divide-white/5">
                <div
                    v-for="item in cartItems"
                    :key="item.product.id"
                    class="flex gap-4 bg-white px-4 py-4 dark:bg-slate-800/30"
                >
                    <!-- Product Image -->
                    <div
                        class="size-20 shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-slate-700/50"
                    >
                        <img
                            :src="item.product.image"
                            :alt="item.product.name"
                            class="size-full object-cover"
                            @error="handleImageError(item.product.id, $event)"
                        />
                    </div>

                    <!-- Product Details -->
                    <div class="flex flex-1 flex-col">
                        <div class="mb-1 flex items-start justify-between">
                            <h3 class="font-bold leading-tight">
                                {{ item.product.name }}
                                <span
                                    v-if="getDiscountPercentage(item.product) !== null"
                                    class="text-secondary"
                                >
                                    (descuento {{ getDiscountPercentage(item.product) }}%)
                                </span>
                            </h3>
                            <button
                                class="p-1 text-gray-400 transition-colors hover:text-red-500"
                                @click="removeFromCart(item.product.id)"
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
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                        <p class="mb-2 text-xs text-gray-400">{{ item.product.description }}</p>

                        <div class="mt-auto flex items-center justify-between">
                            <div>
                                <div class="flex items-baseline gap-2">
                                    <span
                                        v-if="hasActiveDiscount(item.product)"
                                        class="text-sm text-gray-400 line-through"
                                    >
                                        {{ formatPrice(item.product.salePrice) }}
                                    </span>
                                    <span
                                        class="text-lg font-extrabold"
                                        :class="hasActiveDiscount(item.product) ? 'text-secondary' : ''"
                                    >
                                        {{ hasActiveDiscount(item.product) ? `(${formatPrice(item.price)})` : formatPrice(item.price) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <span>Subtotal: </span>
                                    <span
                                        v-if="hasActiveDiscount(item.product)"
                                        class="line-through"
                                    >
                                        {{ formatPrice(item.product.salePrice * item.quantity) }}
                                    </span>
                                    <span
                                        :class="hasActiveDiscount(item.product) ? 'text-secondary' : ''"
                                    >
                                        {{ hasActiveDiscount(item.product) ? `(${formatPrice(item.price * item.quantity)})` : formatPrice(item.price * item.quantity) }}
                                    </span>
                                </p>
                            </div>

                            <!-- Quantity Selector -->
                            <div class="flex items-center gap-1">
                                <button
                                    class="flex size-8 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 dark:border-white/10 dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600"
                                    @click="decrementQuantity(item.product.id)"
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
                                            d="M20 12H4"
                                        />
                                    </svg>
                                </button>
                                <span class="w-8 text-center text-sm font-bold">
                                    {{ item.quantity }}
                                </span>
                                <button
                                    class="flex size-8 items-center justify-center rounded-full bg-primary text-white transition-colors hover:bg-primary/90"
                                    @click="incrementQuantity(item.product.id)"
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
                                            d="M12 4v16m8-8H4"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </Deferred>
        </main>

        <!-- Order Summary & Checkout -->
        <div
            v-if="cartItems.length > 0"
            class="inset-x-0 bottom-0 z-40 border-t border-gray-100 bg-white px-4 pb-4 pt-4 dark:border-white/5 dark:bg-slate-900"
        >
            <div class="mb-4 space-y-2">
                <div class="flex items-center justify-between pt-1">
                    <span class="text-lg font-bold uppercase">Subtotal del carrito</span>
                    <div class="flex items-baseline gap-2">
                        <span
                            v-if="cartHasDiscount"
                            class="text-sm text-gray-400 line-through"
                        >
                            {{ formatPrice(originalCartSubtotal) }}
                        </span>
                        <span
                            class="text-2xl font-extrabold"
                            :class="cartHasDiscount ? 'text-secondary' : 'text-primary'"
                        >
                            {{ cartHasDiscount ? `(${formatPrice(total)})` : formatPrice(total) }}
                        </span>
                    </div>
                </div>
            </div>

            <button
                @click="goToCheckout"
                :disabled="isProcessing"
                :class="[
                    'flex w-full items-center justify-center gap-2 rounded-xl py-4 font-bold text-white shadow-lg transition-colors',
                    isProcessing
                        ? 'cursor-not-allowed bg-primary/70'
                        : 'bg-primary shadow-primary/30 hover:bg-primary/90',
                ]"
            >
                <template v-if="isProcessing">
                    <span>Procesando</span>
                    <span class="flex gap-0.5">
                        <span class="inline-block size-1.5 animate-bounce rounded-full bg-white" style="animation-delay: 0ms"></span>
                        <span class="inline-block size-1.5 animate-bounce rounded-full bg-white" style="animation-delay: 150ms"></span>
                        <span class="inline-block size-1.5 animate-bounce rounded-full bg-white" style="animation-delay: 300ms"></span>
                    </span>
                </template>
                <template v-else>
                    Proceder al checkout
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
                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                        />
                    </svg>
                </template>
            </button>
        </div>

        </div>

        <!-- Login Modal -->
        <LoginModal
            v-if="showLoginModal"
            redirect-to="/checkout"
            @close="showLoginModal = false"
        />
    </MobileLayout>
</template>
