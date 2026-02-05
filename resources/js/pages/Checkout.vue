<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

const paymentMethod = ref('card');
</script>

<template>
    <Head title="Checkout" />

    <MobileLayout active-nav="cart">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- Header (Standardized) -->
            <header class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900">
                <div class="flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/cart"
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <svg
                                class="size-6 text-slate-900 dark:text-white"
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
                        <h1 class="text-lg font-bold">Checkout</h1>
                    </div>
                    <div class="flex items-center">
                        <button
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <svg
                                class="size-6 text-slate-900 dark:text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- Shipping Address -->
                <section class="mb-6">
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Shipping Address
                    </h2>
                    <div
                        class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex gap-3">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-primary dark:bg-blue-900/20"
                                >
                                    <span class="material-symbols-outlined">location_on</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 dark:text-white">Home</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        1234 Main St, Apt 4B<br />
                                        New York, NY 10001
                                    </p>
                                </div>
                            </div>
                            <button class="font-bold text-primary text-sm">Edit</button>
                        </div>
                    </div>
                </section>

                <!-- Payment Method -->
                <section class="mb-6">
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Payment Method
                    </h2>
                    <div class="flex flex-col gap-3">
                        <label
                            class="flex cursor-pointer items-center gap-4 rounded-2xl border bg-white p-4 shadow-sm transition-colors dark:bg-slate-800/50"
                            :class="paymentMethod === 'card' ? 'border-blue-600 ring-1 ring-blue-600 dark:border-blue-500' : 'border-slate-200 dark:border-white/5'"
                        >
                            <input
                                type="radio"
                                name="payment"
                                value="card"
                                v-model="paymentMethod"
                                class="hidden"
                            />
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300"
                            >
                                <span class="material-symbols-outlined">credit_card</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-slate-900 dark:text-white">Credit Card</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">**** **** **** 4242</p>
                            </div>
                            <div
                                class="size-5 rounded-full border-2 flex items-center justify-center"
                                :class="paymentMethod === 'card' ? 'border-blue-600 bg-primary' : 'border-slate-300 dark:border-slate-600'"
                            >
                                <div v-if="paymentMethod === 'card'" class="size-2 rounded-full bg-white"></div>
                            </div>
                        </label>

                        <label
                            class="flex cursor-pointer items-center gap-4 rounded-2xl border bg-white p-4 shadow-sm transition-colors dark:bg-slate-800/50"
                            :class="paymentMethod === 'apple' ? 'border-blue-600 ring-1 ring-blue-600 dark:border-blue-500' : 'border-slate-200 dark:border-white/5'"
                        >
                            <input
                                type="radio"
                                name="payment"
                                value="apple"
                                v-model="paymentMethod"
                                class="hidden"
                            />
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-full bg-black text-white"
                            >
                                <span class="material-symbols-outlined">phone_iphone</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-slate-900 dark:text-white">Apple Pay</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Fast & Secure</p>
                            </div>
                            <div
                                class="size-5 rounded-full border-2 flex items-center justify-center"
                                :class="paymentMethod === 'apple' ? 'border-blue-600 bg-primary' : 'border-slate-300 dark:border-slate-600'"
                            >
                                <div v-if="paymentMethod === 'apple'" class="size-2 rounded-full bg-white"></div>
                            </div>
                        </label>
                    </div>
                </section>

                <!-- Order Summary -->
                <section>
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Summary
                    </h2>
                    <div
                        class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800/50 border border-slate-200 dark:border-white/5 space-y-3"
                    >
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Subtotal</span>
                            <span class="font-medium text-slate-900 dark:text-white">$13.69</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Shipping</span>
                            <span class="font-medium text-slate-900 dark:text-white">$2.50</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Tax</span>
                            <span class="font-medium text-slate-900 dark:text-white">$1.20</span>
                        </div>
                        <div
                            class="border-t border-slate-100 pt-3 dark:border-white/5 flex justify-between items-center"
                        >
                            <span class="font-bold text-slate-900 dark:text-white">Total</span>
                            <span class="text-xl font-extrabold text-primary">$17.39</span>
                        </div>
                    </div>
                </section>
            <!-- Bottom Action Bar -->
            <div
                class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-100 bg-white px-4 pb-24 pt-4 dark:border-white/5 dark:bg-slate-900"
            >
                <button
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-4 font-bold text-white shadow-lg shadow-primary/30 transition-colors hover:bg-primary/90"
                >
                    Pay $17.39
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
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                </button>
            </div>
            </main>

            <!-- Bottom Action Bar -->

        </div>
    </MobileLayout>
</template>
