<template>
    <ShopLayout>
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col gap-6 md:flex-row">
                <!-- Sidebar -->
                <aside class="w-full md:w-1/4">
                    <!-- Categories Card -->
                    <div class="mb-6 rounded-lg bg-white p-5 shadow-md transition-shadow hover:shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Categories</h3>
                        <ul class="space-y-3">
                            <li v-for="category in categories" :key="category.id">
                                <Link
                                    :href="route('shop.categories.show', { slug: category.slug })"
                                    class="text-gray-700 transition-colors hover:text-blue-600"
                                >
                                    {{ category.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Filters Card -->
                    <div class="rounded-lg bg-white p-5 shadow-md transition-shadow hover:shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Filters</h3>
                        <form @submit.prevent="applyFilters">
                            <div class="mb-4">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Price Range</label>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="number"
                                        v-model="filters.minPrice"
                                        placeholder="Min"
                                        class="focus:ring-opacity-50 w-full rounded-md border border-gray-300 p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:outline-none"
                                    />
                                    <span class="text-gray-500">-</span>
                                    <input
                                        type="number"
                                        v-model="filters.maxPrice"
                                        placeholder="Max"
                                        class="focus:ring-opacity-50 w-full rounded-md border border-gray-300 p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:outline-none"
                                    />
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="focus:ring-opacity-50 w-full rounded-md bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700 focus:ring focus:ring-blue-500 focus:outline-none"
                            >
                                Apply Filters
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Products Grid -->
                <main class="w-full md:w-3/4">
                    <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                        <h1 class="text-2xl font-bold text-gray-800">{{ title }}</h1>
                        <div class="flex items-center">
                            <label for="sort-select" class="mr-2 text-sm text-gray-700">Sort by:</label>
                            <select
                                id="sort-select"
                                v-model="sortBy"
                                @change="sortProducts"
                                class="focus:ring-opacity-50 rounded-md border border-gray-300 p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:outline-none"
                            >
                                <option value="newest">Newest</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div v-if="products.data.length" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <ProductCard v-for="product in products.data" :key="product.id" :product="product" @add-to-cart="addToCart" />
                    </div>

                    <div v-else class="flex h-64 items-center justify-center rounded-lg bg-gray-100">
                        <p class="text-lg text-gray-500">No products found</p>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8" v-if="products.links && products.links.length > 3">
                        <Pagination :links="products.links" />
                    </div>
                </main>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { defineEmits, defineProps, ref } from 'vue';
import ShopLayout from '../shop/ShopApp.vue';
import Pagination from '../shop/components/Pagination.vue';
import ProductCard from '../shop/components/ProductCard.vue';

const emit = defineEmits(['update:filters']);

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    title: {
        type: String,
        default: 'All Products',
    },
    initialFilters: {
        type: Object,
        default: () => ({
            minPrice: null,
            maxPrice: null,
        }),
    },
});

// Reactive state
const filters = ref({
    minPrice: props.initialFilters.minPrice,
    maxPrice: props.initialFilters.maxPrice,
});

const sortBy = ref('newest');

// Methods
const addToCart = (product) => {
    // Using a more modern approach with route composition API
    router.post(
        '/cart/add',
        {
            product_id: product.id,
            quantity: 1,
        },
        {
            onSuccess: () => {
                console.log('success');
            },
        },
    );
};

const applyFilters = () => {
    router.get(
        '/shop/products',
        {
            min_price: filters.value.minPrice,
            max_price: filters.value.maxPrice,
            sort: sortBy.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const sortProducts = () => {
    router.get(
        '/shop/products',
        {
            sort: sortBy.value,
            min_price: filters.value.minPrice,
            max_price: filters.value.maxPrice,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
</script>
