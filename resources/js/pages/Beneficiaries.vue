<script setup lang="ts">
import MobileLayout from '@/layouts/MobileLayout.vue';
import { Head, Link, WhenVisible, router } from '@inertiajs/vue3';
import { User, ChevronLeft, MapPin, Phone, Mail, IdCard, UserPlus, Pencil } from 'lucide-vue-next';

interface Municipality {
    id: number;
    name: string;
    province: {
        id: number;
        name: string;
        slug: string;
    };
}

interface Beneficiary {
    id: number;
    name: string;
    identity_number: string;
    email: string | null;
    phone: string;
    address: string;
    customer_id: number;
    municipality_id: number;
    created_at: string;
    updated_at: string;
    municipality: Municipality;
}

const props = defineProps<{
    beneficiaries: Beneficiary[];
    beneficiariesNextPageUrl: string | null;
    currentPage: number;
    lastPage: number;
    total: number;
    error?: string;
}>();

const loadMore = () => {
    if (props.beneficiariesNextPageUrl) {
        router.get(props.beneficiariesNextPageUrl, {}, {
            preserveState: true,
            preserveScroll: true,
        });
    }
};

const formatPhone = (phone: string) => {
    if (!phone) return '';
    if (phone.startsWith('53')) {
        return `+${phone.slice(0, 2)} ${phone.slice(2)}`;
    }
    return phone;
};
</script>

<template>
    <Head title="Mis Beneficiarios" />

    <MobileLayout active-nav="profile" :show-chat-button="false">
        <div class="flex flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white">
            <!-- TopAppBar -->
            <header class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/profile"
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <ChevronLeft class="size-6 text-slate-900 dark:text-white" />
                        </Link>
                        <h1 class="text-lg font-bold">Mis Beneficiarios ({{ total }})</h1>
                    </div>
                    <Link
                        href="/beneficiaries/create"
                        class="flex items-center gap-1.5 rounded-full bg-primary px-3 py-1.5 text-sm font-semibold text-white dark:text-black shadow-sm transition-colors hover:bg-primary/90"
                    >
                        <UserPlus class="size-4" />
                        <span>Nuevo</span>
                    </Link>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-24 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- Error State -->
                <div v-if="error" class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-4">
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
                </div>

                <!-- Empty State -->
                <div v-if="!beneficiaries || beneficiaries.length === 0" class="flex flex-col items-center justify-center py-16">
                    <div class="bg-slate-100 dark:bg-slate-800 rounded-full p-6 mb-4">
                        <User class="size-12 text-slate-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">No tienes beneficiarios</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-center text-sm mb-6 px-4">
                        Los beneficiarios son las personas a quienes envías tus pedidos. Agrega uno durante el proceso de compra.
                    </p>
                    <Link
                        href="/"
                        class="bg-primary text-white dark:text-black px-6 py-3 rounded-xl font-semibold text-sm hover:bg-primary/90 transition-colors"
                    >
                        Explorar productos
                    </Link>
                </div>

                <!-- Beneficiaries List -->
                <div v-else class="space-y-4">
                    <div
                        v-for="beneficiary in beneficiaries"
                        :key="beneficiary.id"
                        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm overflow-hidden"
                    >
                        <!-- Beneficiary Header -->
                        <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center size-12 rounded-full bg-primary/10 text-primary">
                                    <User class="size-6" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold text-slate-900 dark:text-white truncate">
                                        {{ beneficiary.name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ beneficiary.municipality.name }}, {{ beneficiary.municipality.province.name }}
                                    </p>
                                </div>
                                <Link
                                    :href="`/beneficiaries/${beneficiary.id}/edit`"
                                    class="flex items-center justify-center size-10 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-primary/10 hover:text-primary transition-colors"
                                >
                                    <Pencil class="size-4" />
                                </Link>
                            </div>
                        </div>

                        <!-- Beneficiary Details -->
                        <div class="p-4 space-y-3">
                            <!-- Identity Number -->
                            <div class="flex items-start gap-3">
                                <div class="flex items-center justify-center size-8 rounded-lg bg-slate-100 dark:bg-slate-700 shrink-0">
                                    <IdCard class="size-4 text-slate-500 dark:text-slate-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Carnet de Identidad</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ beneficiary.identity_number }}
                                    </p>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex items-start gap-3">
                                <div class="flex items-center justify-center size-8 rounded-lg bg-slate-100 dark:bg-slate-700 shrink-0">
                                    <Phone class="size-4 text-slate-500 dark:text-slate-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Teléfono</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ formatPhone(beneficiary.phone) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Email (if exists) -->
                            <div v-if="beneficiary.email" class="flex items-start gap-3">
                                <div class="flex items-center justify-center size-8 rounded-lg bg-slate-100 dark:bg-slate-700 shrink-0">
                                    <Mail class="size-4 text-slate-500 dark:text-slate-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Correo electrónico</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                        {{ beneficiary.email }}
                                    </p>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="flex items-start gap-3">
                                <div class="flex items-center justify-center size-8 rounded-lg bg-slate-100 dark:bg-slate-700 shrink-0">
                                    <MapPin class="size-4 text-slate-500 dark:text-slate-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Dirección</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white whitespace-pre-line">
                                        {{ beneficiary.address }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Infinite Scroll Trigger -->
                    <WhenVisible
                        v-if="beneficiariesNextPageUrl"
                        @appear="loadMore"
                    >
                        <div class="flex items-center justify-center py-4">
                            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                <svg class="size-5 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Cargando más beneficiarios...</span>
                            </div>
                        </div>
                    </WhenVisible>

                    <!-- End of list indicator -->
                    <div v-else-if="beneficiaries.length > 0" class="text-center py-4">
                        <p class="text-sm text-slate-400 dark:text-slate-500">
                            Has llegado al final de la lista
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </MobileLayout>
</template>
