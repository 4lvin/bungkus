<template>
    <shop-layout>
        <!-- Hero Section -->
        <section class="mb-8 bg-blue-600 px-4 py-12 text-white">
            <div class="container mx-auto text-center">
                <h1 class="mb-4 text-4xl font-bold">Welcome to Our E-Commerce Store</h1>
                <p class="mb-6 text-xl">Discover amazing products at great prices</p>
                <Link
                    :href="route('shop.products.index')"
                    class="inline-block rounded-lg bg-white px-6 py-3 font-semibold text-blue-600 transition hover:bg-gray-100"
                >
                    Shop Now
                </Link>
            </div>
        </section>

        <!-- Featured Products dengan Grid yang Diperbaiki -->
        <section class="container mx-auto mb-16 px-4">
            <div class="mb-8 flex items-center justify-between">
                <h2 class="text-2xl font-bold md:text-3xl">Produk Unggulan</h2>
                <Link :href="route('shop.products.index')" class="text-blue-600 transition hover:text-blue-800"> Lihat Semua &rarr; </Link>
            </div>

            <!-- Grid produk yang diperbaiki -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <div
                    v-for="product in featuredProducts"
                    :key="product.id"
                    class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-md transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="relative h-56 overflow-hidden bg-gray-200">
                        <img
                            v-if="product.image"
                            :src="product.image"
                            alt="Product"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center bg-gray-100">
                            <span class="text-gray-500">No image</span>
                        </div>
                        <div class="absolute top-3 right-3">
                            <button
                                @click.prevent="addToWishlist(product)"
                                class="rounded-full bg-white p-2 text-gray-600 shadow-md transition hover:bg-blue-600 hover:text-white"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <div class="mb-1 flex items-center gap-1">
                            <span v-for="i in 5" :key="i" class="text-yellow-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    />
                                </svg>
                            </span>
                            <span class="text-xs text-gray-500">({{ Math.floor(Math.random() * 50) + 10 }} ulasan)</span>
                        </div>
                        <p class="mb-1 text-sm font-medium text-blue-600">{{ product.category.name }}</p>
                        <h3 class="mb-2 text-lg font-bold">{{ product.name }}</h3>
                        <div class="mt-auto mb-4 flex items-center justify-between">
                            <span class="text-xl font-bold">Rp{{ formatPrice(product.price) }}</span>
                            <span v-if="Math.random() > 0.5" class="text-sm text-gray-500 line-through">
                                Rp{{ formatPrice(Math.floor(product.price * 1.2)) }}
                            </span>
                        </div>
                        <button
                            @click="addToCart(product)"
                            class="w-full rounded-full bg-blue-600 py-3 font-medium text-white transition hover:bg-blue-700"
                        >
                            Tambahkan ke Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bagian Banner tetap sama -->

        <!-- Categories dengan Grid yang Diperbaiki -->
        <section class="container mx-auto mb-16 px-4">
            <div class="mb-8 flex items-center justify-between">
                <h2 class="text-2xl font-bold md:text-3xl">Kategori Belanja</h2>
                <!-- <Link :href="route('shop.categories.index')" class="text-blue-600 transition hover:text-blue-800">
                    Lihat Semua &rarr;
                </Link> -->
            </div>

            <!-- Grid kategori yang diperbaiki -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="group relative overflow-hidden rounded-2xl bg-gray-900 shadow-lg transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    <!-- Background kategori -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>

                    <Link :href="route('shop.categories.show', category.slug)" class="relative block h-64 w-full p-6 text-white">
                        <div class="flex h-full flex-col justify-end">
                            <h3 class="mb-2 text-2xl font-bold transition group-hover:text-blue-200">{{ category.name }}</h3>
                            <p class="text-sm text-gray-300">{{ category.description }}</p>
                            <div class="mt-4 flex items-center">
                                <span class="text-sm font-medium">Jelajahi</span>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="ml-2 h-5 w-5 transition group-hover:translate-x-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Bagian lainnya tetap sama -->
    </shop-layout>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import ShopLayout from '../shop/ShopApp.vue';

defineProps({
    featuredProducts: Array,
    categories: Array,
});

function addToCart(product) {
    router.post(
        route('cart.add'),
        {
            product_id: product.id,
            quantity: 1,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Added to cart successfully');
            },
            onError: (errors) => {
                console.error('Error adding to cart:', errors);
            },
        },
    );
}

function addToWishlist(product) {
    router.post(
        route('wishlist.add'),
        {
            product_id: product.id,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                // Tambahkan notifikasi sukses di sini jika diinginkan
            },
        },
    );
}

// Format harga dengan titik sebagai pemisah ribuan
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>

<style scoped>
/* Memastikan card produk memiliki tinggi yang sama */
.group {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Membuat ruang untuk konten */
.group .p-5 {
    display: flex;
    flex-direction: column;
    flex: 1;
}

/* Animasi untuk transisi halus */
.group {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

/* Memastikan grid bekerja dengan baik di semua ukuran layar */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}

@media (min-width: 640px) and (max-width: 768px) {
    .grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 768px) and (max-width: 1024px) {
    .grid.grid-cols-3,
    .grid.lg\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .grid.grid-cols-4,
    .grid.lg\:grid-cols-4 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (min-width: 1024px) {
    .grid.lg\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .grid.lg\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}
</style>
