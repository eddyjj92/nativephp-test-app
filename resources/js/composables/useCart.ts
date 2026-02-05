import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { add, update, remove, clear } from '@/routes/cart';

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

const isProcessing = ref(false);
const pendingRequests = ref(0);

export function useCart() {
    const page = usePage();

    const getCart = () => page.props.cart as CartProps;

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

    const addToCart = (product: any, quantity: number = 1) => {
        const cart = getCart();
        
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

        startRequest();
        router.post(add().url, {
            product_id: product.id,
            quantity,
        }, {
            preserveScroll: true,
            preserveState: true,
            only: ['cart'],
            onFinish: endRequest,
            onError: () => {
                cart.count = previousCount;
                cart.total = previousTotal;
                cart.items = previousItems;
            },
        });
    };

    const updateQuantity = (productId: number, newQuantity: number) => {
        const cart = getCart();
        const item = cart.items.find((i: CartItem) => i.product?.id === productId);
        if (!item) return;

        const previousCount = cart.count;
        const previousTotal = cart.total;
        const previousQuantity = item.quantity;
        const diff = newQuantity - previousQuantity;

        item.quantity = newQuantity;
        cart.count += diff;
        cart.total += item.price * diff;

        startRequest();
        router.post(update(productId).url, {
            _method: 'PUT',
            quantity: newQuantity,
        }, {
            preserveScroll: true,
            preserveState: true,
            only: ['cart'],
            onFinish: endRequest,
            onError: () => {
                item.quantity = previousQuantity;
                cart.count = previousCount;
                cart.total = previousTotal;
            },
        });
    };

    const incrementQuantity = (productId: number) => {
        const cart = getCart();
        const item = cart.items.find((i: CartItem) => i.product?.id === productId);
        if (item) {
            updateQuantity(productId, item.quantity + 1);
        }
    };

    const decrementQuantity = (productId: number) => {
        const cart = getCart();
        const item = cart.items.find((i: CartItem) => i.product?.id === productId);
        if (item && item.quantity > 1) {
            updateQuantity(productId, item.quantity - 1);
        }
    };

    const removeFromCart = (productId: number) => {
        const cart = getCart();
        const itemIndex = cart.items.findIndex((i: CartItem) => i.product?.id === productId);
        if (itemIndex === -1) return;

        const previousCount = cart.count;
        const previousTotal = cart.total;
        const previousItems = [...cart.items];
        const item = cart.items[itemIndex];

        cart.count -= item.quantity;
        cart.total -= item.price * item.quantity;
        cart.items.splice(itemIndex, 1);

        startRequest();
        router.post(remove(productId).url, {
            _method: 'DELETE',
        }, {
            preserveScroll: true,
            preserveState: true,
            only: ['cart'],
            onFinish: endRequest,
            onError: () => {
                cart.count = previousCount;
                cart.total = previousTotal;
                cart.items = previousItems;
            },
        });
    };

    const clearCartAction = () => {
        const cart = getCart();

        const previousCount = cart.count;
        const previousTotal = cart.total;
        const previousItems = [...cart.items];

        cart.count = 0;
        cart.total = 0;
        cart.items.length = 0;

        startRequest();
        router.post(clear().url, {
            _method: 'DELETE',
        }, {
            preserveScroll: true,
            preserveState: true,
            only: ['cart'],
            onFinish: endRequest,
            onError: () => {
                cart.count = previousCount;
                cart.total = previousTotal;
                cart.items.push(...previousItems);
            },
        });
    };

    return {
        isProcessing,
        addToCart,
        updateQuantity,
        incrementQuantity,
        decrementQuantity,
        removeFromCart,
        clearCart: clearCartAction,
    };
}
