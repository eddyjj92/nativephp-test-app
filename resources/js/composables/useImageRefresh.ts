import axios from 'axios';
import { refresh } from '@/routes/products';

/**
 * Composable para manejar errores de carga de imágenes (403 Forbidden).
 * Si una imagen falla, verifica el estado y notifica al servidor para refrescar el caché.
 */
export function useImageRefresh() {
    const handleImageError = async (productId: number, event: Event) => {
        const img = event.target as HTMLImageElement;
        
        // Evitar bucles infinitos si la nueva imagen también falla
        if (img.dataset.refreshed) return;

        try {
            // Intentar la petición POST de refresco directamente
            const refreshUrl = refresh(productId).url;
            
            const { data } = await axios.post(refreshUrl);
            
            if (data.success && data.product?.image) {
                img.dataset.refreshed = 'true';
                // Añadir un timestamp para forzar al navegador a recargar la imagen
                // incluso si la URL base es similar
                const separator = data.product.image.includes('?') ? '&' : '?';
                img.src = `${data.product.image}${separator}t=${Date.now()}`;
            }
        } catch (error) {
            console.error('Error refreshing product image:', error);
        }
    };

    return {
        handleImageError
    };
}
