<script setup lang="ts">
import MobileBottomNav from '@/components/MobileBottomNav.vue';
import MobileTopBar from '@/components/MobileTopBar.vue';

type NavId = 'home' | 'catalog' | 'cart' | 'saved' | 'profile';

type Props = {
    activeNav?: NavId;
    cartCount?: number;
};

const props = withDefaults(defineProps<Props>(), {
    activeNav: 'home',
    cartCount: 0,
});
</script>

<template>
    <div class="relative min-h-screen bg-slate-50 pb-24 dark:bg-slate-900">
        <MobileTopBar />
        <div class="pt-2">
            <slot />
        </div>
        <MobileBottomNav :active="props.activeNav" :cart-count="props.cartCount" />
    </div>
</template>
