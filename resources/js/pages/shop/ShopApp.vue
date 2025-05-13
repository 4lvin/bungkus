<template>
    <div class="shop-layout">
        <!-- Header -->
        <header class="bg-green-800 text-white">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between">
                    <Link :href="route('home')" class="text-xl font-bold">Tumbas Online</Link>

                    <nav>
                        <ul class="flex space-x-4">
                            <li>
                                <Link :href="route('shop.products.index')" class="hover:text-gray-200">Products</Link>
                            </li>
                            <li>
                                <Link :href="route('cart.index')" class="relative flex items-center hover:text-gray-200">
                                    Cart
                                    <span
                                        v-if="cartCount > 0"
                                        class="absolute -top-2 -right-4 inline-flex transform items-center justify-center rounded-full bg-red-600 px-2 py-1 text-xs leading-none font-bold text-white"
                                    >
                                        {{ cartCount }}
                                    </span>
                                </Link>
                            </li>
                            <li v-if="auth?.user">
                                <Link :href="route('profile')" class="hover:text-gray-200">My Account</Link>
                            </li>
                            <li v-if="auth?.user">
                                <form @submit.prevent="logout">
                                    <button type="submit" class="hover:text-gray-200">Logout</button>
                                </form>
                            </li>
                            <li v-else>
                                <Link :href="route('login')" class="hover:text-gray-200">Login</Link>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="container mx-auto px-4 py-6">
            <slot></slot>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 py-4 text-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <h3 class="mb-2 text-lg font-bold">About Us</h3>
                        <p>Your trusted online shopping destination for quality products.</p>
                    </div>
                    <div>
                        <h3 class="mb-2 text-lg font-bold">Categories</h3>
                        <ul>
                            <li v-for="category in categories" :key="category.id">
                                <Link :href="route('shop.categories.show', category.slug)" class="hover:text-gray-300">
                                    {{ category.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="mb-2 text-lg font-bold">Contact Us</h3>
                        <p>Email: info@example.com</p>
                        <p>Phone: +1 234 567 890</p>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <p>&copy; 2025 E-Commerce Shop. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const page = usePage();
const auth = page.props.auth;
const categories = ref([]);
const cartCount = ref(0);
console.log(page.props);
const logout = () => {
    router.post(route('logout'));
};

onMounted(() => {
    categories.value = page.props.categories || [];
    cartCount.value = page.props.cartCount || 0;
});
</script>
