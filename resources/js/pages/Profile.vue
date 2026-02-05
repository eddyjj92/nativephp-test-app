<script setup lang="ts">
import { Head, Link, usePage, router, useForm } from '@inertiajs/vue3';
import {
    UserPen,
    Bell,
    ChevronLeft,
    ShoppingBag,
    MapPin,
    Ticket,
    User,
    HelpCircle,
    LogOut,
    ChevronRight,
    Upload,
    X,
} from 'lucide-vue-next';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { camera, on, off, Events } from '#nativephp';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';

import MobileLayout from '@/layouts/MobileLayout.vue';


// Datos del usuario autenticado desde las props compartidas de Inertia
const page = usePage();
const user = computed(() => (page.props.auth as any)?.user ?? null);

const showEditModal = ref(false);
const showLogoutModal = ref(false);

const form = useForm({
    avatar: null as File | string | null,
});

const nativePreviewUrl = ref<string | null>(null);

const selectImage = async () => {
    try {
        // Activamos multiple() para que la interfaz de la galería sea más natural
        // pero mantenemos maxItems(1) si solo queremos una foto de perfil
        await camera.pickImages()
            .id('profile-pic')
            .multiple(false)
            .maxItems(1);
    } catch (error) {
        console.error('Error picking image:', error);
    }
};

const takePhoto = async () => {
    try {
        await camera.getPhoto().id('profile-pic');
    } catch (error) {
        console.error('Error taking photo:', error);
    }
};

const handlePhotoSelected = async (payload: any) => {
    const data = payload;
    
    if (data.id === 'profile-pic' || !data.id) {
        let path = '';
        
        if (data.path) {
            path = data.path;
        } else if (data.files && data.files.length > 0) {
            path = data.files[0].path;
        } else if (data.paths && data.paths.length > 0) {
            path = data.paths[0];
        } else if (Array.isArray(data) && data.length > 0) {
            path = typeof data[0] === 'string' ? data[0] : data[0].path;
        }

        if (path) {
            try {
                const response = await fetch(`/native/preview?path=${encodeURIComponent(path)}`);
                if (response.ok) {
                    const fullBase64 = await response.text();
                    
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const maxDim = 500; // Reducido a 500px para mínima carga
                        let width = img.width;
                        let height = img.height;

                        if (width > maxDim || height > maxDim) {
                            if (width > height) {
                                height *= maxDim / width;
                                width = maxDim;
                            } else {
                                width *= maxDim / height;
                                height = maxDim;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx?.drawImage(img, 0, 0, width, height);
                        
                        // Calidad bajada al 40% (0.4) para un peso mínimo
                        const optimizedBase64 = canvas.toDataURL('image/jpeg', 0.4);
                        nativePreviewUrl.value = optimizedBase64;
                        
                        // Guardamos el Base64 optimizado en el form para subir este en lugar del original
                        form.avatar = optimizedBase64;
                    };
                    img.src = fullBase64;
                }
            } catch (error) {
                console.error('Error procesando imagen:', error);
            }
        }
    }
};

onMounted(() => {
    on(Events.Camera.PhotoTaken, handlePhotoSelected);
    on(Events.Gallery.MediaSelected, handlePhotoSelected);
});

onUnmounted(() => {
    off(Events.Camera.PhotoTaken, handlePhotoSelected);
    off(Events.Gallery.MediaSelected, handlePhotoSelected);
});

const submitAvatar = () => {
    if (!form.avatar) return;

    form.post('/profile/update', {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            form.reset();
            nativePreviewUrl.value = null;
        },
    });
};

const previewUrl = computed(() => {
    if (nativePreviewUrl.value) return nativePreviewUrl.value;
    if (!form.avatar) return null;
    if (typeof form.avatar === 'string') return form.avatar;
    return URL.createObjectURL(form.avatar as Blob);
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
function confirmLogout() {
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
                    <Link
                        href="/orders"
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
                                Ver historial
                            </p>
                        </div>
                    </Link>
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
                    <Link
                        href="/profile/edit"
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
                    </Link>
                    <Link
                        href="/help"
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
                    </Link>
                    <div
                        class="flex items-center gap-4 bg-white dark:bg-slate-800/30 px-4 min-h-[64px] justify-between cursor-pointer active:bg-slate-100 dark:active:bg-slate-800"
                        @click="showLogoutModal = true"
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
                        <h2 class="text-lg font-bold text-white dark:text-black">Editar Perfil</h2>
                        <p class="text-xs text-white/80 dark:text-black">Actualiza tu foto de perfil</p>
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

                        <div class="w-full space-y-3">
                            <div class="flex gap-3">
                                <button
                                    @click="selectImage"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 py-4 text-sm font-bold text-slate-600 transition-colors hover:border-primary hover:text-primary dark:border-white/10 dark:text-slate-400"
                                >
                                    <Upload class="size-6" />
                                    Galería
                                </button>
                                <button
                                    @click="takePhoto"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 py-4 text-sm font-bold text-slate-600 transition-colors hover:border-primary hover:text-primary dark:border-white/10 dark:text-slate-400"
                                >
                                    <UserPen class="size-6" />
                                    Cámara
                                </button>
                            </div>

                            <button
                                @click="submitAvatar"
                                :disabled="!form.avatar || form.processing"
                                class="flex w-full items-center justify-center rounded-xl bg-primary py-3.5 font-bold text-white shadow-lg shadow-primary/30 transition-transform active:scale-[0.98] disabled:opacity-50 disabled:shadow-none"
                            >
                                <span v-if="form.processing">Actualizando...</span>
                                <span v-else class="dark:text-black">Guardar Cambios</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Confirmar Logout -->
        <div
            v-if="showLogoutModal"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
            @click.self="showLogoutModal = false"
        >
            <div class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-slate-900">
                <div class="p-6 text-center">
                    <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-950/30">
                        <LogOut class="size-8 text-red-500" />
                    </div>
                    <h2 class="mb-2 text-lg font-bold text-slate-900 dark:text-white">Cerrar Sesión</h2>
                    <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">
                        ¿Estás seguro de que deseas cerrar sesión?
                    </p>
                    <div class="flex gap-3">
                        <button
                            @click="showLogoutModal = false"
                            class="flex-1 rounded-xl border border-slate-200 bg-white py-3 font-semibold text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="confirmLogout"
                            class="flex-1 rounded-xl bg-red-500 py-3 font-semibold text-white transition-colors hover:bg-red-600"
                        >
                            Cerrar Sesión
                        </button>
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