<template>
    <article class="group overflow-hidden rounded-lg bg-white shadow-md transition-shadow hover:shadow-lg">
        <Link :href="route('shop.products.show', { slug: product.slug })">
            <div class="relative h-56 bg-gray-100">
                <img
                    v-if="product.image"
                    :src="product.image"
                    :alt="product.name"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
                <div v-else class="flex h-full w-full items-center justify-center">
                    <span class="text-gray-500">No image</span>
                </div>

                <div v-if="product.discount_percent" class="absolute top-2 left-2 rounded-full bg-red-500 px-2 py-1 text-xs font-bold text-white">
                    -{{ product.discount_percent }}%
                </div>
            </div>
        </Link>

        <div class="p-4">
            <Link :href="route('shop.products.show', { slug: product.slug })">
                <h3 class="mb-1 text-lg font-semibold text-gray-800 transition-colors group-hover:text-blue-600">
                    {{ product.name }}
                </h3>
            </Link>

            <p class="mb-3 text-sm text-gray-600">{{ product.category.name }}</p>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg font-bold text-gray-900">Rp. {{ formatPrice(product.price) }}</span>
                    <span v-if="product.original_price" class="text-sm text-gray-500 line-through"> ${{ formatPrice(product.original_price) }} </span>
                </div>

                <button
                    @click.prevent="$emit('add-to-cart', product)"
                    class="focus:ring-opacity-50 rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring focus:ring-green-500 focus:outline-none"
                >
                    Add to Cart
                </button>
            </div>
        </div>
    </article>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { defineEmits, defineProps } from 'vue';

defineEmits(['add-to-cart']);

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

function formatPrice(price) {
    // Convert to number and remove decimal part
    const wholeNumber = Math.floor(Number(price));

    // Format with dot as thousand separator
    return wholeNumber.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>
