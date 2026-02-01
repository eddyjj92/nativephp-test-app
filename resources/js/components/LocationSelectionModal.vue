<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';

type Province = {
    id: string;
    name: string;
};

type Municipality = {
    id: string;
    name: string;
    provinceId: string;
};

const emit = defineEmits(['close']);
const page = usePage();
// If showLocationModal prop is true, it means it's forced by middleware -> not closable
const isForced = computed(() => !!page.props.showLocationModal);

const provinces = ref<Province[]>([]);
const municipalities = ref<Municipality[]>([]);
const loading = ref(true);

const form = useForm({
    province: '',
    municipality: '',
});

// Fetch data on mount
onMounted(async () => {
    try {
        const response = await axios.get('/locations/data');
        provinces.value = response.data.provinces;
        municipalities.value = response.data.municipalities;
        
        // Pre-fill if exists in page props (for manual change)
        const currentLoc = page.props.location as any;
        if (currentLoc?.province) form.province = currentLoc.province;
        if (currentLoc?.municipality) form.municipality = currentLoc.municipality;
        
    } catch (error) {
        console.error('Failed to load location data', error);
    } finally {
        loading.value = false;
    }
});

const filteredMunicipalities = computed(() => {
    if (!form.province) return [];
    return municipalities.value.filter(m => m.provinceId === form.province);
});

const submit = () => {
    form.post('/locations/set', {
        onSuccess: () => {
            // Force reload to update middleware state effectively or let Inertia handle it via prop update
            window.location.reload(); 
        }
    });
};

const handleBackgroundClick = () => {
    if (!isForced.value) {
        emit('close');
    }
};
</script>

<template>
    <div 
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click.self="handleBackgroundClick"
    >
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-slate-900">
            <div class="flex items-center justify-between bg-blue-600 px-6 py-4">
                <div>
                    <h2 class="text-lg font-bold text-white">Select Location</h2>
                    <p class="text-xs text-blue-100">To show you available products</p>
                </div>
                <button v-if="!isForced" @click="$emit('close')" class="rounded-full bg-blue-500 p-1 text-white hover:bg-blue-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-4 p-6">
                <div v-if="loading" class="flex justify-center py-8">
                    <div class="size-8 animate-spin rounded-full border-4 border-slate-200 border-t-blue-600"></div>
                </div>

                <template v-else>
                    <!-- Province -->
                    <div class="space-y-1">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Province</label>
                        <div class="relative">
                            <select
                                v-model="form.province"
                                class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-white/10 dark:bg-slate-800 dark:text-white"
                                @change="form.municipality = ''"
                            >
                                <option value="" disabled>Select a province</option>
                                <option v-for="prov in provinces" :key="prov.id" :value="prov.id">
                                    {{ prov.name }}
                                </option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-500">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Municipality -->
                    <div class="space-y-1">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Municipality</label>
                        <div class="relative">
                            <select
                                v-model="form.municipality"
                                :disabled="!form.province"
                                class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 disabled:opacity-50 dark:border-white/10 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="" disabled>Select a municipality</option>
                                <option v-for="muni in filteredMunicipalities" :key="muni.id" :value="muni.id">
                                    {{ muni.name }}
                                </option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-500">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="!form.province || !form.municipality || form.processing"
                        class="mt-2 flex w-full items-center justify-center rounded-xl bg-blue-600 py-3.5 font-bold text-white shadow-lg shadow-blue-600/30 transition-transform active:scale-[0.98] disabled:opacity-50 disabled:shadow-none"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Confirm Location</span>
                    </button>
                </template>
            </form>
        </div>
    </div>
</template>
