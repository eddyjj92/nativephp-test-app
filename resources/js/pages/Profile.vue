<script setup lang="ts">
import { Head, Link, usePage, router, useForm } from '@inertiajs/vue3';
import MobileLayout from '@/layouts/MobileLayout.vue';
import { computed, ref } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    UserPen,
    Bell,
    ChevronLeft,
    ShoppingBag,
    MapPin,
    CreditCard,
    Ticket,
    User,
    HelpCircle,
    LogOut,
    ChevronRight,
    Upload,
    X,
} from 'lucide-vue-next';

// Datos del usuario autenticado desde las props compartidas de Inertia
const page = usePage();
const user = computed(() => (page.props.auth as any)?.user ?? null);

const showEditModal = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    avatar: null as File | null,
});

const onFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.avatar = target.files[0];
    }
};

const submitAvatar = () => {
    if (!form.avatar) return;

    form.post('/profile/update', {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            form.reset();
        },
    });
};

const previewUrl = computed(() => {
    return form.avatar ? URL.createObjectURL(form.avatar) : null;
});

function getInitials(name: string = '') {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
}

// Logout usando Fortify (POST /logout)
function logout() {
    router.post('/logout');
}
</script>

<template>
    <Head title="User Profile - Compay Market" />

    <MobileLayout active-nav="profile">
        <div
            class="nativephp-safe-area flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-900 dark:text-white"
        >
            <!-- TopAppBar -->
            <header class="fixed top-[calc(var(--inset-top,0px)+100px)] left-0 right-0 z-40 bg-slate-50 pb-2 pt-3 dark:bg-slate-900">

                <div class="mb-3 flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <Link
                            href="/"
                            class="rounded-full p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10"
                        >
                            <ChevronLeft class="size-6 text-slate-900 dark:text-white" />
                        </Link>
                        <h1 class="text-lg font-bold">Profile</h1>
                    </div>
                    <div class="flex items-center">

                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 px-4 pb-4 pt-[calc(var(--inset-top,0px)+60px)]">
                <!-- ProfileHeader -->
                <div class="flex px-4 pb-4 @container">
                    <div class="flex w-full flex-col gap-4 items-center">
                        <div class="flex flex-col items-center gap-2">
                            <Avatar class="size-32 border-4 border-primary/10">
                                <AvatarImage
                                    v-if="user?.avatar"
                                    :src="user.avatar"
                                    :alt="user.name"
                                    class="object-cover"
                                />
                                <AvatarFallback class="text-3xl bg-slate-100 dark:bg-slate-800 font-bold text-slate-500">
                                    {{ getInitials(user?.name) }}
                                </AvatarFallback>
                            </Avatar>
                            
                            <Button
                                @click="showEditModal = true"
                                variant="outline"
                                size="sm"
                                class="h-8 gap-1 rounded-full px-3 border-primary text-primary hover:bg-primary/5 dark:hover:bg-primary/10"
                            >
                                <UserPen class="size-4" />
                                <span class="text-xs font-bold">Editar Foto</span>
                            </Button>

                            <div class="mt-1 flex flex-col items-center justify-center">
                                <h2 class="text-xl font-semibold">
                                    {{ user?.name ?? 'User' }}
                                </h2>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">
                                    {{ user?.email ?? '' }}
                                </p>
                                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">
                                    Miembro desde {{ new Date(user?.created_at ?? '').toLocaleDateString(undefined, { year: 'numeric', month: 'long' }) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TextGrid (Quick Actions) -->
                <div class="grid grid-cols-2 gap-3 p-4">
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-800/50 p-4 flex-col cursor-pointer transition-all active:scale-95 shadow-sm"
                    >
                        <div class="text-primary" data-icon="ShoppingBag">
                            <ShoppingBag class="size-6" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-slate-900 dark:text-white text-base font-bold leading-tight">
                                Mis Ordenes
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-normal leading-normal">
                                3 Ordenes Pagadas
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-800/50 p-4 flex-col cursor-pointer transition-all active:scale-95 shadow-sm"
                    >
                        <div class="text-primary" data-icon="MapPin">
                            <MapPin class="size-6" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-slate-900 dark:text-white text-base font-bold leading-tight">
                                Beneficiarios
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-normal leading-normal">
                                2 Beneficiarios
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-slate-800/50 p-4 flex-col cursor-pointer transition-all active:scale-95 shadow-sm"
                    >
                        <div class="text-primary" data-icon="Ticket">
                            <Ticket class="size-6" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <h2 class="text-slate-900 dark:text-white text-base font-bold leading-tight">
                                Descuentos
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-normal leading-normal">
                                1 Disponible
                            </p>
                        </div>
                    </div>
                </div>
                <!-- SectionHeader -->
                <div class="mt-2">
                    <h3 class="text-slate-900 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">
                        Ajustes de la Cuenta
                    </h3>
                </div>
                <!-- ListItems -->
                <div class="flex flex-col">
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-slate-800/30 px-4 min-h-[64px] justify-between cursor-pointer active:bg-slate-100 dark:active:bg-slate-800 border-b border-slate-50 dark:border-white/5"
                    >
                        <div class="flex items-center gap-4">
                            <div class="text-primary flex items-center justify-center rounded-xl bg-primary/10 shrink-0 size-10">
                                <User class="size-6" />
                            </div>
                            <p class="text-slate-900 dark:text-white text-base font-semibold leading-normal flex-1 truncate">
                                Informacion Personal
                            </p>
                        </div>
                        <div class="shrink-0">
                            <ChevronRight class="size-6 text-slate-400" />
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-slate-800/30 px-4 min-h-[64px] justify-between cursor-pointer active:bg-slate-100 dark:active:bg-slate-800 border-b border-slate-50 dark:border-white/5"
                    >
                        <div class="flex items-center gap-4">
                            <div class="text-primary flex items-center justify-center rounded-xl bg-primary/10 shrink-0 size-10">
                                <HelpCircle class="size-6" />
                            </div>
                            <p class="text-slate-900 dark:text-white text-base font-semibold leading-normal flex-1 truncate">
                                Centro de Ayuda
                            </p>
                        </div>
                        <div class="shrink-0">
                            <ChevronRight class="size-6 text-slate-400" />
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-slate-800/30 px-4 min-h-[64px] justify-between cursor-pointer active:bg-slate-100 dark:active:bg-slate-800"
                        @click="logout"
                    >
                        <div class="flex items-center gap-4">
                            <div class="text-red-500 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-950/30 shrink-0 size-10">
                                <LogOut class="size-6" />
                            </div>
                            <p class="text-red-500 text-base font-semibold leading-normal flex-1 truncate">
                                Cerrar Session
                            </p>
                        </div>
                        <div class="shrink-0">
                            <ChevronRight class="size-6 text-slate-400" />
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modal Editar Foto -->
        <div
            v-if="showEditModal"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
            @click.self="showEditModal = false"
        >
            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-slate-900">
                <div class="flex items-center justify-between bg-primary px-6 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">Editar Perfil</h2>
                        <p class="text-xs text-white/80">Actualiza tu foto de perfil</p>
                    </div>
                    <button @click="showEditModal = false" class="rounded-full bg-white/20 p-1 text-white hover:bg-white/30">
                        <X class="size-5" />
                    </button>
                </div>

                <div class="p-6">
                    <div class="flex flex-col items-center gap-6">
                        <!-- Preview -->
                        <div class="relative">
                            <Avatar class="size-32 border-4 border-primary/10">
                                <AvatarImage
                                    v-if="previewUrl"
                                    :src="previewUrl"
                                    class="object-cover"
                                />
                                <AvatarImage
                                    v-else-if="user?.avatar"
                                    :src="user.avatar"
                                    :alt="user.name"
                                    class="object-cover"
                                />
                                <AvatarFallback class="text-3xl bg-slate-100 dark:bg-slate-800 font-bold text-slate-500">
                                    {{ getInitials(user?.name) }}
                                </AvatarFallback>
                            </Avatar>
                        </div>

                        <div class="w-full space-y-4">
                            <button
                                @click="fileInput?.click()"
                                class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 py-4 text-sm font-bold text-slate-600 transition-colors hover:border-primary hover:text-primary dark:border-white/10 dark:text-slate-400"
                            >
                                <Upload class="size-6" />
                                {{ form.avatar ? 'Cambiar imagen' : 'Seleccionar imagen' }}
                            </button>

                            <input
                                ref="fileInput"
                                type="file"
                                class="hidden"
                                accept="image/*"
                                @change="onFileChange"
                            />

                            <button
                                @click="submitAvatar"
                                :disabled="!form.avatar || form.processing"
                                class="flex w-full items-center justify-center rounded-xl bg-primary py-3.5 font-bold text-white shadow-lg shadow-primary/30 transition-transform active:scale-[0.98] disabled:opacity-50 disabled:shadow-none"
                            >
                                <span v-if="form.processing">Actualizando...</span>
                                <span v-else>Guardar Cambios</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MobileLayout>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
