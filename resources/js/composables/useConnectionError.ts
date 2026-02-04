import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const isOffline = ref(false);
const errorMessage = ref<string | null>(null);
const lastFailedUrl = ref<string | null>(null);

export function useConnectionError() {
    function showError(message?: string, url?: string) {
        isOffline.value = true;
        errorMessage.value = message || null;
        lastFailedUrl.value = url || null;
    }

    function hideError() {
        isOffline.value = false;
        errorMessage.value = null;
    }

    function retry() {
        hideError();

        if (lastFailedUrl.value) {
            router.visit(lastFailedUrl.value);
        } else {
            window.location.reload();
        }
    }

    return {
        isOffline,
        errorMessage,
        showError,
        hideError,
        retry,
    };
}

export function setupConnectionErrorInterceptor() {
    const { showError } = useConnectionError();

    router.on('invalid', (event) => {
        // Server returned invalid response (could be network issue)
        const response = event.detail.response;
        const status = response?.status;

        // Check for our custom error header or server errors
        const isCustomError =
            response?.headers?.get('X-Inertia-Error') === 'true';

        if (!status || status === 0 || status >= 500 || isCustomError) {
            showError(
                'Error al conectar con el servidor.',
                window.location.href,
            );
            // Prevent Inertia's default modal
            event.preventDefault();
        }
    });

    router.on('exception', (event) => {
        // Request exception (timeout, network error)
        showError(
            'La solicitud tardó demasiado. Verifica tu conexión.',
            window.location.href,
        );
        event.preventDefault();
    });

    // Listen for online/offline events
    window.addEventListener('offline', () => {
        showError('Se perdió la conexión a internet.');
    });

    window.addEventListener('online', () => {
        const { hideError } = useConnectionError();
        hideError();
    });
}
