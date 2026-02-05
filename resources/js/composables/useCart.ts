import { router, usePage } from '@inertiajs/vue3';
import { add } from '@/routes/cart';

interface CartItem {
    product: any;
    quantity: number;
    price: number;
}

interface CartProps {
    items: CartItem[];
    count: number;
    total: number;
}

export function useCart() {
    const page = usePage();

    const addToCart = (product: any, quantity: number = 1) => {
        const cart = page.props.cart as CartProps;
        
        const previousCount = cart.count;
        const previousTotal = cart.total;
        const previousItems = [...cart.items];

        const existingItem = cart.items.find(item => item.product?.id === product.id);
        const price = product.salePrice ?? product.price;

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.items.push({
                product,
                quantity,
                price,
            });
        }
        cart.count += quantity;
        cart.total += price * quantity;

        router.post(add().url, {
            product_id: product.id,
            quantity,
        }, {
            preserveScroll: true,
            preserveState: true,
            only: ['cart'],
            onError: () => {
                cart.count = previousCount;
                cart.total = previousTotal;
                cart.items = previousItems;
            },
        });
    };

    return {
        addToCart,
    };
}
