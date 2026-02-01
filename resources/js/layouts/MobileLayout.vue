<script setup lang="ts">
import MobileBottomNav from '@/components/MobileBottomNav.vue';

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
    <div class="relative min-h-screen">
        <slot />
        <MobileBottomNav :active="props.activeNav" :cart-count="props.cartCount" />
    </div>
</template>
