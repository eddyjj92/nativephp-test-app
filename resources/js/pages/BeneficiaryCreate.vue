<script setup lang="ts">
import MobileLayout from '@/layouts/MobileLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, User, IdCard, Phone, Mail, MapPin, Building } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

interface Municipality {
    id: number;
    name: string;
}

interface Province {
    id: number;
    name: string;
    slug: string;
    municipalities: Municipality[];
}

const props = defineProps<{
    provinces: Province[];
}>();

const form = useForm({
    name: '',
    identity_number: '',
    email: '',
    phone: '',
    address: '',
    municipality_id: null as number | null,
});

const selectedProvinceId = ref<number | null>(null);

const municipalities = computed(() => {
    if (!selectedProvinceId.value) return [];
    const province = props.provinces.find(p => p.id === selectedProvinceId.value);
    return province?.municipalities ?? [];
});

watch(selectedProvinceId, () => {
    form.municipality_id = null;
});

const submit = () => {
    form.post('/beneficiaries', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nuevo Beneficiario" />

    <MobileLayout active-nav="profile" :show-chat-button="false">
        <div class="flex flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white">
            <!-- TopAppBar -->
            <header class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/beneficiaries"
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <ChevronLeft class="size-6 text-slate-900 dark:text-white" />
                        </Link>
                        <h1 class="text-lg font-bold">Nuevo Beneficiario</h1>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-24 pt-[calc(var(--inset-top,0px)+60px)]">
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Name -->
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <User class="size-4" />
                            Nombre completo *
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="Nombre del beneficiario"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-white/10 dark:bg-slate-800 dark:text-white"
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Identity Number -->
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <IdCard class="size-4" />
                            Carnet de Identidad *
                        </label>
                        <input
                            v-model="form.identity_number"
                            type="text"
                            required
                            placeholder="Ej: 85010112345"
                            maxlength="11"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-white/10 dark:bg-slate-800 dark:text-white"
                        />
                        <p v-if="form.errors.identity_number" class="text-xs text-red-500">{{ form.errors.identity_number }}</p>
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <Phone class="size-4" />
                            Teléfono *
                        </label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            required
                            placeholder="Ej: 53123456"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-white/10 dark:bg-slate-800 dark:text-white"
                        />
                        <p v-if="form.errors.phone" class="text-xs text-red-500">{{ form.errors.phone }}</p>
                    </div>

                    <!-- Email (optional) -->
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <Mail class="size-4" />
                            Correo electrónico (opcional)
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="correo@ejemplo.com"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-white/10 dark:bg-slate-800 dark:text-white"
                        />
                        <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- Province Selector -->
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <Building class="size-4" />
                            Provincia *
                        </label>
                        <select
                            v-model="selectedProvinceId"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-white/10 dark:bg-slate-800 dark:text-white"
                        >
                            <option :value="null" disabled>Selecciona una provincia</option>
                            <option v-for="province in provinces" :key="province.id" :value="province.id">
                                {{ province.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Municipality Selector -->
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <MapPin class="size-4" />
                            Municipio *
                        </label>
                        <select
                            v-model="form.municipality_id"
                            required
                            :disabled="!selectedProvinceId"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-primary focus:ring-1 focus:ring-primary disabled:opacity-50 dark:border-white/10 dark:bg-slate-800 dark:text-white"
                        >
                            <option :value="null" disabled>
                                {{ selectedProvinceId ? 'Selecciona un municipio' : 'Primero selecciona una provincia' }}
                            </option>
                            <option v-for="municipality in municipalities" :key="municipality.id" :value="municipality.id">
                                {{ municipality.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.municipality_id" class="text-xs text-red-500">{{ form.errors.municipality_id }}</p>
                    </div>

                    <!-- Address -->
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <MapPin class="size-4" />
                            Dirección *
                        </label>
                        <textarea
                            v-model="form.address"
                            required
                            rows="3"
                            placeholder="Calle, número, entre calles, referencias..."
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-white/10 dark:bg-slate-800 dark:text-white resize-none"
                        ></textarea>
                        <p v-if="form.errors.address" class="text-xs text-red-500">{{ form.errors.address }}</p>
                    </div>

                    <!-- General Error -->
                    <div v-if="form.errors.error" class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl p-4">
                        <p class="text-red-600 dark:text-red-400 text-sm">{{ form.errors.error }}</p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-4 font-bold text-white dark:text-black shadow-lg shadow-primary/30 transition-all hover:bg-primary/90 disabled:opacity-50 disabled:shadow-none"
                    >
                        <span v-if="form.processing">Guardando...</span>
                        <span v-else>Guardar Beneficiario</span>
                    </button>
                </form>
            </main>
        </div>
    </MobileLayout>
</template>
