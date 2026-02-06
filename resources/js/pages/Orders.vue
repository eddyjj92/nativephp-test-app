<script setup lang="ts">
import MobileLayout from '@/layouts/MobileLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Package, ChevronDown, ChevronLeft, Clock, CheckCircle, XCircle, Truck, AlertCircle, Info } from 'lucide-vue-next';
import { ref } from 'vue';

interface CartItem {
    id: number;
    product_name: string;
    quantity: number;
    unit_price: string;
    product: {
        id: number;
        name: string;
        image: string;
    } | null;
}

interface Beneficiary {
    id: number;
    name: string;
    phone: string;
    address: string;
    municipality: {
        id: number;
        name: string;
        province: {
            id: number;
            name: string;
        };
    };
}

interface DeliveryType {
    id: number;
    name: string;
    delivery_times: string;
}

interface Order {
    id: number;
    order_number: string;
    products_cost: string;
    shipping_cost: string;
    currency: string;
    status: string;
    redirect_url?: string | null;
    created_at: string;
    beneficiary: Beneficiary;
    delivery_type: DeliveryType;
    cart_items: CartItem[];
}

interface OrdersData {
    current_page: number;
    data: Order[];
    last_page: number;
    per_page: number;
    total: number;
    next_page_url: string | null;
    prev_page_url: string | null;
}

const props = defineProps<{
    orders: OrdersData;
    error?: string;
}>();

const getStatusConfig = (status: string) => {
    const configs: Record<string, { label: string; color: string; bgColor: string; icon: typeof CheckCircle }> = {
        CREATED: { label: 'Creada', color: 'text-primary', bgColor: 'bg-primary/10 dark:bg-primary/20', icon: Info },
        PENDING: { label: 'Pendiente', color: 'text-yellow-600', bgColor: 'bg-yellow-50 dark:bg-yellow-950/30', icon: Clock },
        PAID: { label: 'Pagado', color: 'text-primary', bgColor: 'bg-blue-50 dark:bg-blue-950/30', icon: CheckCircle },
        COMPLETED: { label: 'Completado', color: 'text-green-600', bgColor: 'bg-green-50 dark:bg-green-950/30', icon: CheckCircle },
        DISPATCHED: { label: 'Despachado', color: 'text-blue-600', bgColor: 'bg-blue-50 dark:bg-blue-950/30', icon: Truck },
        DELIVERED: { label: 'Entregado', color: 'text-blue-600', bgColor: 'bg-blue-50 dark:bg-blue-950/30', icon: CheckCircle },
        CANCELLED: { label: 'Cancelado', color: 'text-red-600', bgColor: 'bg-red-50 dark:bg-red-950/30', icon: XCircle },
        EXPIRED: { label: 'Expirado', color: 'text-slate-500', bgColor: 'bg-slate-100 dark:bg-slate-800', icon: AlertCircle },
    };
    return configs[status] || { label: status, color: 'text-slate-600', bgColor: 'bg-slate-100', icon: Package };
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const formatPrice = (price: string | number, currency: string) => {
    const numPrice = typeof price === 'string' ? parseFloat(price) : price;
    return `${numPrice.toFixed(2)} ${currency}`;
};

const getTotalPrice = (order: Order) => {
    const products = parseFloat(order.products_cost);
    const shipping = parseFloat(order.shipping_cost);
    return formatPrice(products + shipping, order.currency);
};

const getItemsCount = (order: Order) => {
    return order.cart_items.reduce((acc, item) => acc + item.quantity, 0);
};

const expandedOrders = ref<Set<number>>(new Set());

const toggleOrderDetails = (orderId: number) => {
    if (expandedOrders.value.has(orderId)) {
        expandedOrders.value.delete(orderId);
    } else {
        expandedOrders.value.add(orderId);
    }
};

const isExpanded = (orderId: number) => expandedOrders.value.has(orderId);
</script>

<template>
    <Head title="Mis Pedidos" />

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
                        <h1 class="text-lg font-bold">Mis Ordenes</h1>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-24 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- Error State -->
                <div v-if="error" class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-4">
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
                </div>

                <!-- Empty State -->
                <div v-if="!orders.data || orders.data.length === 0" class="flex flex-col items-center justify-center py-16">
                    <div class="bg-slate-100 dark:bg-slate-800 rounded-full p-6 mb-4">
                        <Package class="size-12 text-slate-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">No tienes pedidos</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-center text-sm mb-6">
                        Cuando realices tu primera compra, aparecerá aquí.
                    </p>
                    <Link
                        href="/"
                        class="bg-primary text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-primary/90 transition-colors"
                    >
                        Explorar productos
                    </Link>
                </div>

                <!-- Orders List -->
                <div v-else class="space-y-4">
                    <div
                        v-for="order in orders.data"
                        :key="order.id"
                        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm overflow-hidden"
                    >
                        <!-- Order Header -->
                        <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                        #{{ order.order_number }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ formatDate(order.created_at) }}
                                    </p>
                                </div>
                                <div :class="[getStatusConfig(order.status).bgColor, 'px-3 py-1 rounded-full flex items-center gap-1.5']">
                                    <component :is="getStatusConfig(order.status).icon" :class="[getStatusConfig(order.status).color, 'size-3.5']" />
                                    <span :class="[getStatusConfig(order.status).color, 'text-xs font-medium']">
                                        {{ getStatusConfig(order.status).label }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Beneficiary Info -->
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ order.beneficiary.name }}</p>
                                <p>{{ order.beneficiary.municipality.name }}, {{ order.beneficiary.municipality.province.name }}</p>
                            </div>
                            <div
                                v-if="order.status === 'CREATED' && order.redirect_url"
                                class="mt-2 rounded-lg border border-primary/20 bg-primary/5 p-2.5"
                            >
                                <p class="text-xs font-medium text-primary">Pendiente a pago</p>
                                <a
                                    :href="order.redirect_url"
                                    class="text-xs text-primary underline underline-offset-2"
                                >
                                    {{ order.redirect_url }}
                                </a>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-slate-700 dark:text-slate-300">
                                        {{ getItemsCount(order) }} artículos
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Envío: {{ order.delivery_type.name }} ({{ order.delivery_type.delivery_times }})
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Total</p>
                                    <p class="text-base font-bold text-slate-900 dark:text-white">
                                        {{ getTotalPrice(order) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Toggle Details Button -->
                        <button
                            @click="toggleOrderDetails(order.id)"
                            class="w-full p-3 flex items-center justify-center gap-2 text-sm text-primary font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"
                        >
                            <span>{{ isExpanded(order.id) ? 'Ocultar detalles' : 'Ver detalles' }}</span>
                            <ChevronDown 
                                class="size-4 transition-transform duration-200" 
                                :class="{ 'rotate-180': isExpanded(order.id) }" 
                            />
                        </button>

                        <!-- Expanded Details -->
                        <div 
                            v-if="isExpanded(order.id)" 
                            class="border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50"
                        >
                            <!-- Cart Items List -->
                            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                                <div 
                                    v-for="item in order.cart_items" 
                                    :key="item.id"
                                    class="p-3 flex items-center justify-between"
                                >
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-slate-700 dark:text-slate-300 truncate">
                                            {{ item.product_name }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ item.quantity }} x {{ formatPrice(item.unit_price, order.currency) }}
                                        </p>
                                    </div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white ml-3">
                                        {{ formatPrice(parseFloat(item.unit_price) * item.quantity, order.currency) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Order Totals -->
                            <div class="p-3 border-t border-slate-200 dark:border-slate-600 space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Subtotal</span>
                                    <span class="text-slate-700 dark:text-slate-300">{{ formatPrice(order.products_cost, order.currency) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Envío</span>
                                    <span class="text-slate-700 dark:text-slate-300">{{ formatPrice(order.shipping_cost, order.currency) }}</span>
                                </div>
                                <div class="flex justify-between text-sm font-semibold pt-1 border-t border-slate-200 dark:border-slate-600">
                                    <span class="text-slate-900 dark:text-white">Total</span>
                                    <span class="text-slate-900 dark:text-white">{{ getTotalPrice(order) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="orders.last_page > 1" class="flex items-center justify-center gap-2 pt-4">
                        <Link
                            v-if="orders.prev_page_url"
                            :href="`/orders?page=${orders.current_page - 1}`"
                            class="px-4 py-2 bg-white dark:bg-slate-800 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm"
                        >
                            Anterior
                        </Link>
                        <span class="text-sm text-slate-500 dark:text-slate-400">
                            Página {{ orders.current_page }} de {{ orders.last_page }}
                        </span>
                        <Link
                            v-if="orders.next_page_url"
                            :href="`/orders?page=${orders.current_page + 1}`"
                            class="px-4 py-2 bg-white dark:bg-slate-800 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm"
                        >
                            Siguiente
                        </Link>
                    </div>
                </div>
            </main>
        </div>
    </MobileLayout>
</template>
