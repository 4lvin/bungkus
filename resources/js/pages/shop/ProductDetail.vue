<template>
    <shop-layout>
        <div class="container mx-auto px-4">
            <nav class="mb-4 py-2" aria-label="Breadcrumb">
                <ol class="flex text-sm">
                    <li>
                        <inertia-link :href="route('home')" class="text-blue-600 hover:text-blue-800">Home</inertia-link>
                        <span class="mx-2">/</span>
                    </li>
                    <li>
                        <inertia-link :href="route('shop.products.index')" class="text-blue-600 hover:text-blue-800">Products</inertia-link>
                        <span class="mx-2">/</span>
                    </li>
                    <li>
                        <inertia-link :href="route('shop.categories.show', product.category.slug)" class="text-blue-600 hover:text-blue-800">
                            {{ product.category.name }}
                        </inertia-link>
                        <span class="mx-2">/</span>
                    </li>
                    <li class="font-medium text-gray-600">{{ product.name }}</li>
                </ol>
            </nav>

            <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                <div class="md:flex">
                    <!-- Product Image -->
                    <div class="p-4 md:w-1/2">
                        <div class="flex justify-center">
                            <div class="h-80 w-full rounded bg-gray-200">
                                <img v-if="product.image" :src="product.image" alt="Product" class="h-full w-full object-contain" />
                                <div v-else class="flex h-full w-full items-center justify-center">
                                    <span class="text-gray-500">No image available</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Information -->
                    <div class="p-6 md:w-1/2">
                        <h1 class="mb-2 text-3xl font-bold">{{ product.name }}</h1>
                        <div class="mb-4 text-gray-600">
                            <inertia-link :href="route('shop.categories.show', product.category.slug)" class="hover:text-blue-600">
                                {{ product.category.name }}
                            </inertia-link>
                        </div>

                        <div class="mb-4 text-2xl font-bold text-blue-600">${{ product.price }}</div>

                        <div v-if="product.quantity > 0" class="mb-4 text-green-600">
                            <span class="font-medium">In Stock</span>
                            <span class="text-gray-600"> ({{ product.quantity }} available)</span>
                        </div>
                        <div v-else class="mb-4 text-red-600">Out of Stock</div>

                        <div class="mb-6">
                            <div class="mb-4 flex items-center">
                                <label for="quantity" class="mr-4 font-medium">Quantity:</label>
                                <div class="flex">
                                    <button @click="decrementQuantity" class="rounded-l bg-gray-200 px-3 py-1 hover:bg-gray-300">-</button>
                                    <input
                                        type="number"
                                        id="quantity"
                                        v-model="quantity"
                                        min="1"
                                        :max="product.quantity"
                                        class="w-16 border-t border-b border-gray-300 text-center"
                                    />
                                    <button @click="incrementQuantity" class="rounded-r bg-gray-200 px-3 py-1 hover:bg-gray-300">+</button>
                                </div>
                            </div>

                            <button
                                @click="addToCart"
                                class="focus:ring-opacity-50 flex w-full items-center justify-center rounded-lg bg-blue-600 py-3 font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :disabled="product.quantity <= 0"
                                :class="{ 'cursor-not-allowed opacity-50': product.quantity <= 0 }"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Description -->
                <div class="border-t p-6">
                    <h2 class="mb-4 text-xl font-bold">Description</h2>
                    <div class="prose max-w-none">
                        <p>{{ product.description || 'No description available.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <!-- <div class="my-12" v-if="relatedProducts.length > 0">
                <h2 class="mb-6 text-2xl font-bold">Related Products</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <div v-for="relatedProduct in relatedProducts" :key="relatedProduct.id" class="overflow-hidden rounded-lg bg-white shadow">
                        <inertia-link :href="route('shop.products.show', relatedProduct.slug)">
                            <div class="h-48 bg-gray-200">
                                <img v-if="relatedProduct.image" :src="relatedProduct.image" alt="Product" class="h-full w-full object-cover" />
                                <div v-else class="flex h-full w-full items-center justify-center">
                                    <span class="text-gray-500">No image</span>
                                </div>
                            </div>
                        </inertia-link>

                        <div class="p-4">
                            <inertia-link :href="route('shop.products.show', relatedProduct.slug)">
                                <h3 class="mb-2 text-lg font-semibold">{{ relatedProduct.name }}</h3>
                            </inertia-link>
                            <p class="mb-2 text-sm text-gray-600">{{ relatedProduct.category.name }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold">${{ relatedProduct.price }}</span>
                                <button @click="addRelatedToCart(relatedProduct)" class="rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700">
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </shop-layout>
</template>

<script>
import ShopLayout from '../shop/ShopApp.vue';

export default {
    components: {
        ShopLayout,
    },
    props: {
        product: Object,
        relatedProducts: Array,
    },
    data() {
        return {
            quantity: 1,
        };
    },
    methods: {
        incrementQuantity() {
            if (this.quantity < this.product.quantity) {
                this.quantity++;
            }
        },
        decrementQuantity() {
            if (this.quantity > 1) {
                this.quantity--;
            }
        },
        addToCart() {
            if (this.product.quantity > 0) {
                this.$inertia.post(route('cart.add'), {
                    product_id: this.product.id,
                    quantity: this.quantity,
                });
            }
        },
        addRelatedToCart(product) {
            if (product.quantity > 0) {
                this.$inertia.post(route('cart.add'), {
                    product_id: product.id,
                    quantity: 1,
                });
            }
        },
    },
};
</script>
