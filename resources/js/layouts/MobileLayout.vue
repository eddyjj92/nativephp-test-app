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
