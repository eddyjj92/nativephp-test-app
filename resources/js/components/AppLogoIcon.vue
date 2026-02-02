<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { HTMLAttributes } from 'vue';
import type { AppPageProps } from '@/types';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();

const page = usePage<AppPageProps>();
const settings = computed(() => page.props.settings);
</script>

<template>
    <div :class="className" v-bind="$attrs">
        <img
            :src="settings.logo_light"
            :alt="settings.site_name"
            class="h-full w-full object-contain dark:hidden"
        />
        <img
            :src="settings.logo_dark"
            :alt="settings.site_name"
            class="h-full w-full object-contain hidden dark:block"
        />
    </div>
</template>
