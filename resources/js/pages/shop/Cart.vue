<template>
    <shop-layout>
        <div class="container mx-auto px-4">
            <h1 class="mb-6 text-3xl font-bold">Shopping Cart</h1>

            <div v-if="props.cartItems.length" class="grid gap-6 md:grid-cols-3">
                <!-- Cart Items -->
                <div class="md:col-span-2">
                    <div class="overflow-x-auto rounded-lg bg-white shadow">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">Product</th>
                                    <th class="px-4 py-3 text-left">Price</th>
                                    <th class="px-4 py-3 text-left">Qty</th>
                                    <th class="px-4 py-3 text-left">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in props.cartItems" :key="item.id" class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="mr-4 h-16 w-16 flex-shrink-0 overflow-hidden rounded bg-gray-100">
                                                <img
                                                    v-if="item.product && item.product.image"
                                                    :src="item.product.image"
                                                    alt="Product"
                                                    class="h-full w-full object-cover"
                                                />
                                                <div v-else class="flex h-full items-center justify-center text-xs text-gray-500">No image</div>
                                            </div>
                                            <div>
                                                <template v-if="item.product">
                                                    <Link
                                                        :href="route('shop.products.show', item.product.slug)"
                                                        class="font-medium text-blue-600 hover:text-blue-800"
                                                    >
                                                        {{ item.product.name }}
                                                    </Link>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">${{ item.price }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <button @click="decrement(item)" class="text-gray-500">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M20 12H4" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                            <input
                                                v-model.number="item.quantity"
                                                @change="update(item)"
                                                class="mx-2 w-12 border text-center"
                                                type="number"
                                                min="1"
                                            />
                                            <button @click="increment(item)" class="text-gray-500">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">${{ (item.price * item.quantity).toFixed(2) }}</td>
                                    <td class="px-4 py-3">
                                        <button @click="remove(item)" class="text-red-600 hover:text-red-800">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex flex-wrap justify-between gap-2">
                        <Link :href="route('shop.products.index')" class="rounded bg-gray-600 px-4 py-2 text-white hover:bg-gray-700">
                            Continue Shopping
                        </Link>
                        <button @click="clear" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">Clear Cart</button>
                    </div>
                </div>

                <!-- Summary -->
                <div class="md:col-span-1">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-4 text-lg font-semibold">Cart Summary</h2>
                        <div class="space-y-2 border-t border-b py-2">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>${{ subtotal.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tax</span>
                                <span>${{ tax.toFixed(2) }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between py-4 text-lg font-bold">
                            <span>Total</span>
                            <span>${{ total.toFixed(2) }}</span>
                        </div>
                        <Link
                            :href="route('checkout.index')"
                            class="inline-block w-full rounded bg-blue-600 px-4 py-2 text-center text-white hover:bg-blue-700"
                        >
                            Proceed to Checkout
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty Cart -->
            <div v-else class="rounded-lg bg-white p-6 text-center shadow">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                    />
                </svg>
                <h2 class="my-4 text-2xl font-semibold">Your cart is empty</h2>
                <p class="mb-6 text-gray-600">Looks like you haven't added any products yet.</p>
                <Link :href="route('shop.products.index')" class="rounded bg-blue-600 px-6 py-3 text-white hover:bg-blue-700"> Start Shopping </Link>
            </div>
        </div>
    </shop-layout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import ShopLayout from '../shop/ShopApp.vue';
const props = defineProps({
    cartItems: {
        type: Array,
        required: true,
    },
});

const subtotal = computed(() => props.cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0));
const tax = computed(() => subtotal.value * 0.1);
const total = computed(() => subtotal.value + tax.value);

function update(item) {
    if (item.quantity < 1) item.quantity = 1;
    router.patch(route('cart.update', item.id), { quantity: item.quantity }, { preserveScroll: true });
}

function increment(item) {
    item.quantity++;
    update(item);
}

function decrement(item) {
    if (item.quantity > 1) {
        item.quantity--;
        update(item);
    }
}

function remove(item) {
    if (confirm('Remove this item from cart?')) {
        router.delete(route('cart.remove', item.id), { preserveScroll: true });
    }
}

function clear() {
    if (confirm('Are you sure you want to clear your cart?')) {
        router.delete(route('cart.clear'), { preserveScroll: true });
    }
}
</script>
