<script setup lang="ts">
import { Deferred, Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { browser } from '#nativephp';
import MobileLayout from '@/layouts/MobileLayout.vue';
import type { AppPageProps } from '@/types';

interface DeliveryType {
    id: number;
    name: string;
    delivery_times: string;
    prefix: string;
    price: number;
    is_default: boolean;
}

interface Beneficiary {
    id: number;
    name: string;
    identity_number: string;
    email: string | null;
    phone: string;
    address: string;
    municipality: {
        name: string;
        cost_ring_id?: number | null;
        costRingId?: number | null;
        cost_ring?: {
            id?: number | null;
        } | null;
        province: {
            name: string;
        };
        delivery_types?: DeliveryType[];
    };
}

interface CartItem {
    product: {
        salePrice: number;
        weight: number;
        freeShipping?: boolean;
        free_shipping?: boolean;
    };
    quantity: number;
    price: number;
}

const props = defineProps<{
    beneficiaries?: Beneficiary[];
}>();

const page = usePage<AppPageProps>();
const showBeneficiaryModal = ref(false);
const showDeliveryTypeModal = ref(false);
const selectedBeneficiaryId = ref<number | null>(null);
const selectedDeliveryTypeId = ref<number | null>(null);
const transportationAmount = ref(0);
const transportationWeightRange = ref('');
const transportationLoading = ref(false);

const beneficiariesList = computed(() => props.beneficiaries ?? []);
const hasBeneficiaries = computed(() => beneficiariesList.value.length > 0);

const selectedBeneficiary = computed(() =>
    beneficiariesList.value.find(
        (beneficiary) => beneficiary.id === selectedBeneficiaryId.value,
    ),
);

const availableDeliveryTypes = computed(() => {
    return selectedBeneficiary.value?.municipality.delivery_types ?? [];
});

const selectedDeliveryType = computed(() => {
    return availableDeliveryTypes.value.find(
        (deliveryType) => deliveryType.id === selectedDeliveryTypeId.value,
    );
});

const cart = computed(() => {
    return (
        (page.props.cart as
            | { items?: CartItem[]; total?: number }
            | undefined) ?? { items: [], total: 0 }
    );
});

const cartItems = computed(() => cart.value.items ?? []);

const discountedSubtotal = computed(() => {
    return Number(cart.value.total ?? 0);
});

const originalSubtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => {
        return (
            sum +
            Number(item.product.salePrice ?? 0) * Number(item.quantity ?? 0)
        );
    }, 0);
});

const hasDiscountedProductsInSubtotal = computed(() => {
    return originalSubtotal.value > discountedSubtotal.value + 0.001;
});

const shippingAmount = computed(() => {
    return Number(selectedDeliveryType.value?.price ?? 0);
});

const orderTotalWeightKg = computed(() => {
    return cartItems.value.reduce((sum, item) => {
        return sum + Number(item.product.weight ?? 0) * Number(item.quantity ?? 0);
    }, 0);
});

const totalWeightKg = computed(() => {
    return cartItems.value.reduce((sum, item) => {
        const hasFreeShipping =
            Boolean(item.product.freeShipping) ||
            Boolean(item.product.free_shipping);

        if (hasFreeShipping) {
            return sum;
        }

        return sum + Number(item.product.weight ?? 0) * Number(item.quantity ?? 0);
    }, 0);
});

const shippingLabel = computed(() => {
    if (!selectedDeliveryType.value) {
        return 'Tipo de envío';
    }

    return `Tipo de envío (${selectedDeliveryType.value.name})`;
});

const totalAmount = computed(() => {
    return discountedSubtotal.value + shippingAmount.value + transportationAmount.value;
});

const formatPrice = (value: number): string => {
    return `$${value.toFixed(2)}`;
};

const formatWeightKg = (value: number): string => {
    return Number(value.toFixed(3)).toString();
};

watch(
    selectedBeneficiary,
    (beneficiary) => {
        const deliveryTypes = beneficiary?.municipality.delivery_types ?? [];
        const defaultType = deliveryTypes.find((type) => type.is_default);
        selectedDeliveryTypeId.value =
            defaultType?.id ?? deliveryTypes[0]?.id ?? null;
    },
    { immediate: true },
);

const chooseBeneficiary = (beneficiaryId: number) => {
    selectedBeneficiaryId.value = beneficiaryId;
    showBeneficiaryModal.value = false;
};

const chooseDeliveryType = (deliveryTypeId: number) => {
    selectedDeliveryTypeId.value = deliveryTypeId;
    showDeliveryTypeModal.value = false;
};

const goToGoogleFromCheckoutPay = async () => {
    const googleUrl = 'https://www.google.com';

    try {
        await browser.open(googleUrl);
    } catch {
        window.location.href = googleUrl;
    }
};

const fetchTransportationCost = async () => {
    const costRingId =
        selectedBeneficiary.value?.municipality.cost_ring_id ??
        selectedBeneficiary.value?.municipality.costRingId ??
        selectedBeneficiary.value?.municipality.cost_ring?.id;
    if (!costRingId || totalWeightKg.value <= 0) {
        transportationAmount.value = 0;
        transportationWeightRange.value = '';
        return;
    }

    transportationLoading.value = true;

    try {
        const params = new URLSearchParams({
            cost_ring_id: String(costRingId),
            weight_kg: totalWeightKg.value.toFixed(3),
            total_cost: discountedSubtotal.value.toFixed(2),
        });

        const response = await fetch(`/cart/transportation-cost?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Failed to fetch transportation cost.');
        }

        const data = (await response.json()) as {
            price: string;
            price_with_discount: string;
            weight_range: string;
            has_discount: boolean;
        };

        const selectedPrice = data.has_discount ? data.price_with_discount : data.price;
        transportationAmount.value = Number.parseFloat(selectedPrice ?? '0') || 0;
        transportationWeightRange.value = data.weight_range ?? '';
    } catch {
        transportationAmount.value = 0;
        transportationWeightRange.value = '';
    } finally {
        transportationLoading.value = false;
    }
};

watch(
    [selectedBeneficiary, totalWeightKg, discountedSubtotal],
    () => {
        void fetchTransportationCost();
    },
    { immediate: true },
);
</script>

<template>
    <Head title="Checkout" />

    <MobileLayout active-nav="cart">
        <div
            class="flex flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- Header (Standardized) -->
            <header
                class="fixed top-[calc(var(--inset-top,0px)+100px)] right-0 left-0 z-40 bg-slate-50 pt-3 pb-2 dark:bg-slate-900"
            >
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
            <main class="flex-1 px-4 pt-[calc(var(--inset-top,0px)+60px)] pb-4">
                <!-- Beneficiary -->
                <section class="mb-6">
                    <h2
                        class="mb-3 text-sm font-bold tracking-wider text-slate-500 uppercase dark:text-slate-400"
                    >
                        Beneficiario
                    </h2>
                    <Deferred data="beneficiaries">
                        <template #fallback>
                            <div
                                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-800/50"
                            >
                                <div class="animate-pulse space-y-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="size-10 rounded-full bg-slate-200 dark:bg-slate-700"
                                        ></div>
                                        <div class="flex-1 space-y-2">
                                            <div
                                                class="h-4 w-36 rounded bg-slate-200 dark:bg-slate-700"
                                            ></div>
                                            <div
                                                class="h-3 w-full rounded bg-slate-200 dark:bg-slate-700"
                                            ></div>
                                            <div
                                                class="h-3 w-2/3 rounded bg-slate-200 dark:bg-slate-700"
                                            ></div>
                                        </div>
                                    </div>
                                    <div
                                        class="h-11 w-full rounded-xl bg-slate-200 dark:bg-slate-700"
                                    ></div>
                                </div>
                            </div>
                        </template>

                        <div
                            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-800/50"
                        >
                            <div
                                v-if="selectedBeneficiary"
                                class="flex items-center justify-between gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-primary dark:bg-blue-900/20"
                                    >
                                        <svg
                                            class="size-7"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"
                                            />
                                            <circle
                                                cx="10"
                                                cy="7"
                                                r="3"
                                                stroke-width="2"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 8v6m3-3h-6"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3
                                            class="font-bold text-slate-900 dark:text-white"
                                        >
                                            {{ selectedBeneficiary.name }}
                                        </h3>
                                        <p
                                            class="text-sm text-slate-500 dark:text-slate-400"
                                        >
                                            {{
                                                selectedBeneficiary.municipality
                                                    .name
                                            }},
                                            {{
                                                selectedBeneficiary.municipality
                                                    .province.name
                                            }}
                                        </p>
                                        <p
                                            class="text-sm text-slate-500 dark:text-slate-400"
                                        >
                                            {{ selectedBeneficiary.address }}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    class="inline-flex h-8 shrink-0 items-center gap-1 rounded-full border border-primary/20 bg-primary/10 px-2.5 text-[11px] font-bold text-primary transition-colors hover:bg-primary/20"
                                    @click="showBeneficiaryModal = true"
                                >
                                    Cambiar
                                    <svg
                                        class="size-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div
                                v-else-if="hasBeneficiaries"
                                class="flex items-center justify-between gap-3"
                            >
                                <p
                                    class="text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Aún no has elegido un beneficiario para este
                                    pedido.
                                </p>
                                <button
                                    class="inline-flex h-8 shrink-0 items-center gap-1 rounded-full border border-primary/20 bg-primary/10 px-2.5 text-[11px] font-bold text-primary transition-colors hover:bg-primary/20"
                                    @click="showBeneficiaryModal = true"
                                >
                                    <svg
                                        class="size-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"
                                        />
                                        <circle
                                            cx="10"
                                            cy="7"
                                            r="3"
                                            stroke-width="2"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M18 12h-8"
                                        />
                                    </svg>
                                    Elegir
                                </button>
                            </div>

                            <div
                                v-else
                                class="flex items-center justify-between gap-3"
                            >
                                <p
                                    class="text-sm text-slate-500 dark:text-slate-400"
                                >
                                    No tienes beneficiarios disponibles.
                                </p>
                                <Link
                                    href="/beneficiaries/create"
                                    class="shrink-0 text-sm font-bold text-primary"
                                >
                                    Crear
                                </Link>
                            </div>
                        </div>
                    </Deferred>
                </section>

                <!-- Delivery Types -->
                <section class="mb-6">
                    <h2
                        class="mb-3 text-sm font-bold tracking-wider text-slate-500 uppercase dark:text-slate-400"
                    >
                        Tipo de envío
                    </h2>
                    <Deferred data="beneficiaries">
                        <template #fallback>
                            <div
                                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-800/50"
                            >
                                <div class="animate-pulse space-y-3">
                                    <div
                                        class="h-4 w-32 rounded bg-slate-200 dark:bg-slate-700"
                                    ></div>
                                    <div
                                        class="h-16 rounded-xl bg-slate-200 dark:bg-slate-700"
                                    ></div>
                                    <div
                                        class="h-16 rounded-xl bg-slate-200 dark:bg-slate-700"
                                    ></div>
                                </div>
                            </div>
                        </template>

                        <div
                            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-800/50"
                        >
                            <div
                                v-if="selectedBeneficiary"
                                class="flex items-center justify-between gap-3"
                            >
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="font-semibold text-slate-900 dark:text-white"
                                    >
                                        {{
                                            selectedDeliveryType
                                                ? selectedDeliveryType.name
                                                : 'Tipo de envío'
                                        }}
                                    </p>
                                    <p
                                        class="text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        {{
                                            selectedDeliveryType
                                                ? selectedDeliveryType.delivery_times
                                                : availableDeliveryTypes.length >
                                                    0
                                                  ? 'Aún no has seleccionado un tipo de envío.'
                                                  : 'Este beneficiario no tiene tipos de envío disponibles.'
                                        }}
                                    </p>
                                </div>
                                <div
                                    v-if="selectedDeliveryType"
                                    class="text-right"
                                >
                                    <p
                                        class="text-sm font-bold text-slate-900 dark:text-white"
                                    >
                                        {{
                                            selectedDeliveryType.price > 0
                                                ? formatPrice(
                                                      selectedDeliveryType.price,
                                                  )
                                                : 'Gratis'
                                        }}
                                    </p>
                                </div>
                                <button
                                    v-if="availableDeliveryTypes.length > 0"
                                    class="inline-flex h-8 shrink-0 items-center gap-1 rounded-full border border-primary/20 bg-primary/10 px-2.5 text-[11px] font-bold text-primary transition-colors hover:bg-primary/20"
                                    @click="showDeliveryTypeModal = true"
                                >
                                    {{
                                        selectedDeliveryType
                                            ? 'Cambiar'
                                            : 'Elegir'
                                    }}
                                    <svg
                                        class="size-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </button>
                                <button
                                    v-else
                                    disabled
                                    class="inline-flex h-8 shrink-0 items-center rounded-full border border-slate-300 px-2.5 text-[11px] font-bold text-slate-400 dark:border-slate-700 dark:text-slate-500"
                                >
                                    Sin opciones
                                </button>
                            </div>

                            <div
                                v-else-if="hasBeneficiaries"
                                class="flex items-center justify-between gap-3"
                            >
                                <p
                                    class="text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Selecciona un beneficiario para ver los
                                    tipos de envío.
                                </p>
                            </div>

                            <p
                                v-else
                                class="text-sm text-slate-500 dark:text-slate-400"
                            >
                                Agrega un beneficiario para habilitar el envío.
                            </p>
                        </div>
                    </Deferred>
                </section>

                <!-- Order Summary -->
                <section>
                    <h2
                        class="mb-3 text-sm font-bold tracking-wider text-slate-500 uppercase dark:text-slate-400"
                    >
                        Resumen
                    </h2>
                    <Deferred data="cart">
                        <template #fallback>
                            <div
                                class="space-y-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-800/50"
                            >
                                <div
                                    class="h-4 w-full animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                                ></div>
                                <div
                                    class="h-4 w-full animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                                ></div>
                                <div
                                    class="h-4 w-full animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                                ></div>
                                <div
                                    class="h-6 w-2/3 animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                                ></div>
                            </div>
                        </template>
                        <div
                            class="space-y-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-800/50"
                        >
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400"
                                    >Subtotal del carrito ({{
                                        formatWeightKg(orderTotalWeightKg)
                                    }}
                                    kg)</span
                                >
                                <span class="flex items-center gap-2">
                                    <span
                                        class="font-medium text-slate-900 dark:text-white"
                                    >
                                        {{ formatPrice(discountedSubtotal) }}
                                    </span>
                                    <span
                                        v-if="hasDiscountedProductsInSubtotal"
                                        class="text-xs text-slate-400 line-through dark:text-slate-500"
                                    >
                                        {{ formatPrice(originalSubtotal) }}
                                    </span>
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400"
                                    >{{ shippingLabel }}</span
                                >
                                <span
                                    class="font-medium text-slate-900 dark:text-white"
                                    >{{ formatPrice(shippingAmount) }}</span
                                >
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400"
                                    >Costo de Transportacion</span
                                >
                                <span
                                    class="font-medium text-slate-900 dark:text-white"
                                    >{{
                                        transportationLoading
                                            ? 'Calculando...'
                                            : formatPrice(transportationAmount)
                                    }}</span
                                >
                            </div>
                            <div
                                v-if="transportationWeightRange"
                                class="-mt-1 text-xs text-slate-500 dark:text-slate-400"
                            >
                                Rango de peso: {{ transportationWeightRange }}
                            </div>
                            <div
                                class="flex items-center justify-between border-t border-slate-100 pt-3 dark:border-white/5"
                            >
                                <span
                                    class="font-bold text-slate-900 dark:text-white"
                                    >Total</span
                                >
                                <span
                                    class="text-xl font-extrabold text-primary"
                                    >{{ formatPrice(totalAmount) }}</span
                                >
                            </div>
                        </div>
                    </Deferred>
                </section>
                <div class="mt-4 pb-4">
                    <button
                        type="button"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-4 font-bold text-white shadow-lg shadow-primary/30 transition-colors hover:bg-primary/90"
                        @click="goToGoogleFromCheckoutPay"
                    >
                        Pay {{ formatPrice(totalAmount) }}
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
        </div>

        <!-- Beneficiaries Modal -->
        <div
            v-if="showBeneficiaryModal"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
            @click.self="showBeneficiaryModal = false"
        >
            <div
                class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-slate-900"
            >
                <div
                    class="flex items-center justify-between bg-primary px-6 py-4"
                >
                    <div>
                        <h2 class="text-lg font-bold text-white">
                            Seleccionar Beneficiario
                        </h2>
                        <p class="text-xs text-white/80">
                            Elige quién recibirá este pedido
                        </p>
                    </div>
                    <button
                        class="flex size-7 items-center justify-center rounded-full bg-secondary text-secondary-foreground hover:bg-secondary/80"
                        @click="showBeneficiaryModal = false"
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
                                stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <div class="max-h-[60vh] overflow-y-auto p-4">
                    <div class="space-y-3">
                        <button
                            v-for="beneficiary in beneficiariesList"
                            :key="beneficiary.id"
                            class="flex w-full items-center gap-3 rounded-xl border p-3 text-left transition-colors"
                            :class="
                                selectedBeneficiaryId === beneficiary.id
                                    ? 'border-primary bg-primary/5 dark:bg-primary/10'
                                    : 'border-slate-200 hover:bg-slate-50 dark:border-white/10 dark:hover:bg-slate-800'
                            "
                            @click="chooseBeneficiary(beneficiary.id)"
                        >
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300"
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
                                        d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"
                                    />
                                    <circle
                                        cx="12"
                                        cy="7"
                                        r="4"
                                        stroke-width="2"
                                    />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p
                                    class="font-semibold text-slate-900 dark:text-white"
                                >
                                    {{ beneficiary.name }}
                                </p>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ beneficiary.municipality.name }},
                                    {{ beneficiary.municipality.province.name }}
                                </p>
                                <p
                                    class="line-clamp-2 text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ beneficiary.address }}
                                </p>
                            </div>
                            <div
                                class="mt-0.5 flex size-5 items-center justify-center rounded-full border-2"
                                :class="
                                    selectedBeneficiaryId === beneficiary.id
                                        ? 'border-primary bg-primary'
                                        : 'border-slate-300 dark:border-slate-600'
                                "
                            >
                                <div
                                    v-if="
                                        selectedBeneficiaryId === beneficiary.id
                                    "
                                    class="size-2 rounded-full bg-white"
                                ></div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Types Modal -->
        <div
            v-if="showDeliveryTypeModal"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
            @click.self="showDeliveryTypeModal = false"
        >
            <div
                class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-slate-900"
            >
                <div
                    class="flex items-center justify-between bg-primary px-6 py-4"
                >
                    <div>
                        <h2 class="text-lg font-bold text-white">
                            Seleccionar Tipo de envío
                        </h2>
                        <p class="text-xs text-white/80">
                            Elige cómo deseas recibir este pedido
                        </p>
                    </div>
                    <button
                        class="flex size-7 items-center justify-center rounded-full bg-secondary text-secondary-foreground hover:bg-secondary/80"
                        @click="showDeliveryTypeModal = false"
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
                                stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <div class="max-h-[60vh] overflow-y-auto p-4">
                    <div class="space-y-3">
                        <button
                            v-for="deliveryType in availableDeliveryTypes"
                            :key="deliveryType.id"
                            class="flex w-full items-center gap-3 rounded-xl border p-3 text-left transition-colors"
                            :class="
                                selectedDeliveryTypeId === deliveryType.id
                                    ? 'border-primary bg-primary/5 dark:bg-primary/10'
                                    : 'border-slate-200 hover:bg-slate-50 dark:border-white/10 dark:hover:bg-slate-800'
                            "
                            @click="chooseDeliveryType(deliveryType.id)"
                        >
                            <div class="min-w-0 flex-1">
                                <p
                                    class="font-semibold text-slate-900 dark:text-white"
                                >
                                    {{ deliveryType.name }}
                                </p>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ deliveryType.delivery_times }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p
                                    class="text-sm font-bold text-slate-900 dark:text-white"
                                >
                                    {{
                                        deliveryType.price > 0
                                            ? formatPrice(deliveryType.price)
                                            : 'Gratis'
                                    }}
                                </p>
                            </div>
                            <div
                                class="mt-0.5 flex size-5 items-center justify-center rounded-full border-2"
                                :class="
                                    selectedDeliveryTypeId === deliveryType.id
                                        ? 'border-primary bg-primary'
                                        : 'border-slate-300 dark:border-slate-600'
                                "
                            >
                                <div
                                    v-if="
                                        selectedDeliveryTypeId ===
                                        deliveryType.id
                                    "
                                    class="size-2 rounded-full bg-white"
                                ></div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </MobileLayout>
</template>
