<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

interface Beneficiary {
    id: number;
    name: string;
    identity_number: string;
    email: string | null;
    phone: string;
    address: string;
    municipality: {
        name: string;
        province: {
            name: string;
        };
    };
}

const props = defineProps<{
    beneficiaries?: Beneficiary[];
}>();

const showBeneficiaryModal = ref(false);
const selectedBeneficiaryId = ref<number | null>(null);

const beneficiariesList = computed(() => props.beneficiaries ?? []);
const hasBeneficiaries = computed(() => beneficiariesList.value.length > 0);

const selectedBeneficiary = computed(() =>
    beneficiariesList.value.find(
        (beneficiary) => beneficiary.id === selectedBeneficiaryId.value,
    ),
);

const chooseBeneficiary = (beneficiaryId: number) => {
    selectedBeneficiaryId.value = beneficiaryId;
    showBeneficiaryModal.value = false;
};
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

                <!-- Order Summary -->
                <section>
                    <h2
                        class="mb-3 text-sm font-bold tracking-wider text-slate-500 uppercase dark:text-slate-400"
                    >
                        Summary
                    </h2>
                    <div
                        class="space-y-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-800/50"
                    >
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400"
                                >Subtotal</span
                            >
                            <span
                                class="font-medium text-slate-900 dark:text-white"
                                >$13.69</span
                            >
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400"
                                >Shipping</span
                            >
                            <span
                                class="font-medium text-slate-900 dark:text-white"
                                >$2.50</span
                            >
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400"
                                >Tax</span
                            >
                            <span
                                class="font-medium text-slate-900 dark:text-white"
                                >$1.20</span
                            >
                        </div>
                        <div
                            class="flex items-center justify-between border-t border-slate-100 pt-3 dark:border-white/5"
                        >
                            <span
                                class="font-bold text-slate-900 dark:text-white"
                                >Total</span
                            >
                            <span class="text-xl font-extrabold text-primary"
                                >$17.39</span
                            >
                        </div>
                    </div>
                </section>
                <div class="mt-4 pb-4">
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
    </MobileLayout>
</template>
