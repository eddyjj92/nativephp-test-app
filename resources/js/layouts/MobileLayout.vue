<script setup lang="ts">
import MobileBottomNav from '@/components/MobileBottomNav.vue';
import MobileTopBar from '@/components/MobileTopBar.vue';
import { computed } from 'vue';

type NavId = 'home' | 'catalog' | 'cart' | 'saved' | 'profile';

type Props = {
    activeNav?: NavId;
    cartCount?: number;
    showTopBar?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    activeNav: 'home',
    cartCount: 0,
    showTopBar: true,
});

const topBarHeight = computed(() => (props.showTopBar ? '112px' : '0px'));
</script>

<template>
    <div
        class="mobile-layout relative min-h-screen bg-slate-50 pb-24 dark:bg-slate-900"
        :style="{ '--mobile-topbar-height': topBarHeight }"
    >
        <MobileTopBar v-if="props.showTopBar" />
        <div
            class="mobile-layout-content"
            :class="props.showTopBar ? 'pt-2' : 'pt-0'"
        >
            <slot />
        </div>
        
        <!-- Floating Chat Button -->
        <button
            class="fixed bottom-24 right-4 z-50 flex size-14 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg shadow-blue-600/30 transition-transform hover:scale-105"
        >
            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                />
            </svg>
        </button>

        <MobileBottomNav :active="props.activeNav" :cart-count="props.cartCount" />
    </div>
</template>

<style scoped>
:global(.mobile-layout-content .sticky) {
    top: var(--mobile-topbar-height) !important;
    z-index: 40;
}

:global(.mobile-top-bar) {
    z-index: 60;
}
</style>
