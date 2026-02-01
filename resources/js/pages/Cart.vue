<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

type CartItem = {
    id: number;
    name: string;
    description: string;
    price: number;
    quantity: number;
    image: string;
};

const cartItems = ref<CartItem[]>([
    {
        id: 1,
        name: 'Organic Avocado',
        description: 'Fresh from Mexico • 2 units',
        price: 4.5,
        quantity: 2,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDsLJNLbDaRdlWrk30bLVXGz2zMdwABe3pWlNpNb5MV0CKXj6qLqO4WCWYwBrNCJwAF2rAqY0s_4yJ6HZ8E9hJJQ0aYQm_Xq7eJpCwQY7JOmA',
    },
    {
        id: 2,
        name: 'Whole Wheat Bread',
        description: 'Artisan sourdough • 800g',
        price: 5.2,
        quantity: 1,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBkdQgUIXRO2xmxQisdRa-xXNF5Xf60N8b147d5kkuAsHkR2B8sxIweopIOP4TQ-ZyafkU2CfP4ZqxpBxgSIVrL-hQXygWvNhurSpaxY1W29qELyh_NYKTClebstrSp-ZInNYMiAINV53-BKWqWJbK6YCaL1sphk566QGSEySsVrkdma9yrFh8Edcf_lzXyn431pIhFDUY8Wo7QHDbYZIIzeyKD0wibiAsBRfHXaOz5MwXWmY14QGmWO0YVxrtlKxPiUre2bKtYgks',
    },
    {
        id: 3,
        name: 'Almond Milk',
        description: 'Unsweetened vanilla • 1L',
        price: 3.99,
        quantity: 3,
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCx8jrIDsQet2gsCPjmllYeBU_c-nkpb_O0bu7E910IcOGwqTbGldHyC6AhFT2sHkACQ4x4cR2T9Mh1IInp8e5aBXI7jn6E0lyyHDGwVIHStqpDWJ9OOicbmpDhOEMZh_uxumz-MzCTqI2RFfHkOzH-UGFhnEpyKptYIrKFtQYFsTO5hFeXPhU3A3irp94FpYbrwyIpg7u9PKwp_yw7f-51g1cRVM88qLCJnpMb9-0zX67a9_dQKvGbsq9BEdT6CQ_OJ9QjGyd-3GQ',
    },
]);

const promoCode = ref('');
const shippingFee = 2.5;

const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
});

const total = computed(() => {
    return subtotal.value + shippingFee;
});

const cartCount = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

function formatPrice(price: number): string {
    return `$${price.toFixed(2)}`;
}

function incrementQuantity(itemId: number) {
    const item = cartItems.value.find((i) => i.id === itemId);
    if (item) {
        item.quantity++;
    }
}

function decrementQuantity(itemId: number) {
    const item = cartItems.value.find((i) => i.id === itemId);
    if (item && item.quantity > 1) {
        item.quantity--;
    }
}

function removeItem(itemId: number) {
    cartItems.value = cartItems.value.filter((i) => i.id !== itemId);
}

function clearCart() {
    cartItems.value = [];
}

function applyPromoCode() {
    // Promo code logic would go here
    console.log('Applying promo code:', promoCode.value);
}
</script>

<template>
    <Head title="Shopping Cart" />

    <div
        class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
    >
        <!-- Header -->
        <header
            class="sticky top-0 z-50 border-b border-gray-100 bg-slate-50 pb-3 pt-4 dark:border-white/5 dark:bg-slate-900"
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
                    <h1 class="text-lg font-bold">Shopping Cart</h1>
                </div>
                <button
                    v-if="cartItems.length > 0"
                    class="text-sm font-semibold text-blue-600"
                    @click="clearCart"
                >
                    Clear All
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 pb-64">
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
                    Your cart is empty
                </h2>
                <p class="mb-6 text-sm text-gray-400">
                    Looks like you haven't added anything yet
                </p>
                <Link
                    href="/products"
                    class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white"
                >
                    Start Shopping
                </Link>
            </div>

            <!-- Cart Items -->
            <div v-else class="divide-y divide-gray-100 dark:divide-white/5">
                <div
                    v-for="item in cartItems"
                    :key="item.id"
                    class="flex gap-4 bg-white px-4 py-4 dark:bg-slate-800/30"
                >
                    <!-- Product Image -->
                    <div
                        class="size-24 shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-slate-700/50"
                    >
                        <img
                            :src="item.image"
                            :alt="item.name"
                            class="size-full object-cover"
                        />
                    </div>

                    <!-- Product Details -->
                    <div class="flex flex-1 flex-col">
                        <div class="mb-1 flex items-start justify-between">
                            <h3 class="font-bold leading-tight">{{ item.name }}</h3>
                            <button
                                class="p-1 text-gray-400 transition-colors hover:text-red-500"
                                @click="removeItem(item.id)"
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
                        <p class="mb-2 text-xs text-gray-400">{{ item.description }}</p>

                        <div class="mt-auto flex items-center justify-between">
                            <span class="text-lg font-extrabold">
                                {{ formatPrice(item.price) }}
                            </span>

                            <!-- Quantity Selector -->
                            <div class="flex items-center gap-1">
                                <button
                                    class="flex size-8 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 dark:border-white/10 dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600"
                                    @click="decrementQuantity(item.id)"
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
                                    class="flex size-8 items-center justify-center rounded-full bg-blue-600 text-white transition-colors hover:bg-blue-700"
                                    @click="incrementQuantity(item.id)"
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

            <!-- Promo Code -->
            <div v-if="cartItems.length > 0" class="px-4 py-6">
                <div
                    class="flex items-center gap-2 rounded-xl border-2 border-dashed border-gray-200 bg-white p-2 dark:border-white/10 dark:bg-slate-800/30"
                >
                    <input
                        v-model="promoCode"
                        type="text"
                        placeholder="Promo code"
                        class="flex-1 border-none bg-transparent px-2 text-sm placeholder-gray-400 focus:ring-0"
                    />
                    <button
                        class="rounded-lg bg-gray-900 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white dark:bg-white dark:text-gray-900"
                        @click="applyPromoCode"
                    >
                        Apply
                    </button>
                </div>
            </div>
        </main>

        <!-- Order Summary & Checkout -->
        <div
            v-if="cartItems.length > 0"
            class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-100 bg-white px-4 pb-24 pt-4 dark:border-white/5 dark:bg-slate-900"
        >
            <div class="mb-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-semibold">{{ formatPrice(subtotal) }}</span>
                </div>
                <div
                    class="flex items-center justify-between border-b border-gray-100 pb-3 text-sm dark:border-white/5"
                >
                    <span class="text-gray-500">Shipping Fee</span>
                    <span class="font-semibold">{{ formatPrice(shippingFee) }}</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="text-lg font-bold">Total</span>
                    <span class="text-2xl font-extrabold text-blue-600">
                        {{ formatPrice(total) }}
                    </span>
                </div>
            </div>

            <button
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-4 font-bold text-white shadow-lg shadow-blue-600/30 transition-colors hover:bg-blue-700"
            >
                Proceed to Checkout
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
            </button>
        </div>

        <!-- Bottom Navigation -->
        <nav
            class="fixed inset-x-0 bottom-0 z-50 flex items-center justify-around border-t border-gray-100 bg-white/90 px-4 pb-6 pt-2 backdrop-blur-md dark:border-white/5 dark:bg-slate-900/95"
        >
            <Link href="/" class="flex flex-col items-center gap-1 text-gray-400">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                </svg>
                <span class="text-[10px] font-bold">Home</span>
            </Link>

            <Link href="/products" class="flex flex-col items-center gap-1 text-gray-400">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
                <span class="text-[10px] font-bold">Search</span>
            </Link>

            <Link
                href="/cart"
                class="relative flex flex-col items-center gap-1 text-blue-600"
            >
                <svg
                    class="size-6"
                    fill="currentColor"
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
                <span class="text-[10px] font-bold">Cart</span>
                <div
                    v-if="cartCount > 0"
                    class="absolute -right-1 -top-1 flex size-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[8px] font-bold text-white dark:border-slate-900"
                >
                    {{ cartCount > 9 ? '9+' : cartCount }}
                </div>
            </Link>

            <button class="flex flex-col items-center gap-1 text-gray-400">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                </svg>
                <span class="text-[10px] font-bold">Profile</span>
            </button>
        </nav>
    </div>
</template>
