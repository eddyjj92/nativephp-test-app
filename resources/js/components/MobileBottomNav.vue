<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { home, cart, favorites, profile } from '@/routes';
import { index as productsIndex } from '@/routes/products';

type NavId = 'home' | 'catalog' | 'cart' | 'saved' | 'profile';

type Props = {
    active?: NavId;
    cartCount?: number;
};

const props = withDefaults(defineProps<Props>(), {
    active: 'home',
    cartCount: 0,
});

const emit = defineEmits(['login-required']);
const page = usePage();
const user = computed(() => page.props.auth?.user);
const favoritesCount = computed(() => (page.props.favorites as any)?.count ?? 0);

function itemClasses(id: NavId): string[] {
    return [
        'flex flex-col items-center gap-1 text-[10px] font-bold',
        props.active === id ? 'text-primary' : 'text-gray-400',
    ];
}

function handleProfileClick() {
    emit('login-required');
}
</script>

<template>
    <nav
        class="fixed inset-x-0 bottom-0 z-50 flex items-center justify-between border-t border-gray-100 bg-white/90 px-6 pb-[calc(var(--inset-bottom,0px)+0.5rem)] pl-[calc(var(--inset-left,0px)+1.5rem)] pr-[calc(var(--inset-right,0px)+1.5rem)] pt-2 backdrop-blur-md dark:border-white/5 dark:bg-slate-900/95"
    >
        <Link :href="home().url" :class="itemClasses('home')">
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
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                />
            </svg>
            <span>Inicio</span>
        </Link>

        <Link :href="productsIndex().url" :class="itemClasses('catalog')">
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
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                />
            </svg>
            <span>Catalogo</span>
        </Link>

        <Link :href="cart().url" :class="['relative', ...itemClasses('cart')]">
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
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                />
            </svg>
            <span>Carrito</span>
            <div
                v-if="props.cartCount > 0"
                class="absolute -right-1 -top-1 flex size-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[8px] font-bold text-white dark:border-slate-900"
            >
                {{ props.cartCount }}
            </div>
        </Link>

        <Link :href="favorites().url" :class="['relative', ...itemClasses('saved')]">
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
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                />
            </svg>
            <span>Favoritos</span>
            <div
                v-if="favoritesCount > 0"
                class="absolute -right-1 -top-1 flex size-4 items-center justify-center rounded-full border-2 border-white bg-red-500 text-[8px] font-bold text-white dark:border-slate-900"
            >
                {{ favoritesCount }}
            </div>
        </Link>

        <Link v-if="user" :href="profile().url" :class="itemClasses('profile')">
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
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
            </svg>
            <span>Perfil</span>
        </Link>
        <div v-else :class="itemClasses('profile')" @click="handleProfileClick">
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
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
            </svg>
            <span>Perfil</span>
        </div>
    </nav>
</template>
