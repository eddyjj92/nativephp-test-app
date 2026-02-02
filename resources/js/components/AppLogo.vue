<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import type { AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
const settings = computed(() => page.props.settings);
</script>

<template>
    <div
        class="flex aspect-square size-20 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-5" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">{{
            settings.site_name
        }}</span>
    </div>
</template>
