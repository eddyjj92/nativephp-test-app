import { router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { add, remove, clear } from '@/routes/favorites';

interface FavoritesProps {
    ids: number[];
    count: number;
}

const isProcessing = ref(false);
const pendingRequests = ref(0);

export function useFavorites() {
    const page = usePage();

    const getFavorites = () => page.props.favorites as FavoritesProps;

    const favoriteIds = computed(() => {
        const favorites = getFavorites();
        return new Set(favorites?.ids?.map((id: number | string) => Number(id)) ?? []);
    });

    const favoritesCount = computed(() => getFavorites()?.count ?? 0);

    const startRequest = () => {
        pendingRequests.value++;
        isProcessing.value = true;
    };

    const endRequest = () => {
        pendingRequests.value--;
        if (pendingRequests.value <= 0) {
            pendingRequests.value = 0;
            isProcessing.value = false;
        }
    };

    const isFavorite = (productId: number): boolean => {
        return favoriteIds.value.has(productId);
    };

    const addToFavorites = (productId: number) => {
        const favorites = getFavorites();
        if (!favorites.ids) favorites.ids = [];
        
        const previousIds = [...favorites.ids];
        const previousCount = favorites.count;

        if (!favorites.ids.includes(productId)) {
            favorites.ids.push(productId);
            favorites.count = (favorites.count || 0) + 1;
        }

        startRequest();
        router.post(add().url, {
            product_id: productId,
        }, {
            preserveScroll: true,
            preserveState: true,
            only: ['favorites'],
            onFinish: endRequest,
            onError: () => {
                favorites.ids = previousIds;
                favorites.count = previousCount;
            },
        });
    };

    const removeFromFavorites = (productId: number) => {
        const favorites = getFavorites();
        
        const previousIds = [...(favorites.ids || [])];
        const previousCount = favorites.count;

        const index = favorites.ids?.indexOf(productId);
        if (index !== undefined && index > -1) {
            favorites.ids.splice(index, 1);
            favorites.count = Math.max(0, (favorites.count || 0) - 1);
        }

        startRequest();
        router.delete(`/favorites/${productId}`, {
            preserveScroll: true,
            preserveState: true,
            only: ['favorites'],
            onFinish: endRequest,
            onError: () => {
                favorites.ids = previousIds;
                favorites.count = previousCount;
            },
        });
    };

    const toggleFavorite = (productId: number) => {
        if (isFavorite(productId)) {
            removeFromFavorites(productId);
            return false;
        } else {
            addToFavorites(productId);
            return true;
        }
    };

    const clearFavorites = () => {
        const favorites = getFavorites();

        const previousIds = [...(favorites.ids || [])];
        const previousCount = favorites.count;

        favorites.ids = [];
        favorites.count = 0;

        startRequest();
        router.delete(clear().url, {
            preserveScroll: true,
            preserveState: true,
            only: ['favorites'],
            onFinish: endRequest,
            onError: () => {
                favorites.ids = previousIds;
                favorites.count = previousCount;
            },
        });
    };

    return {
        isProcessing,
        favoriteIds,
        favoritesCount,
        isFavorite,
        addToFavorites,
        removeFromFavorites,
        toggleFavorite,
        clearFavorites,
    };
}
