<template>
    <div class="shop-layout flex min-h-screen flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between">
                    <Link :href="route('home')" class="text-xl font-bold">Bungkus</Link>

                    <!-- Mobile toggle -->
                    <button @click="isMenuOpen = !isMenuOpen" class="focus:outline-none md:hidden">
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path v-if="!isMenuOpen" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Navigation -->
                    <nav :class="['md:flex md:items-center', isMenuOpen ? 'mt-4 block' : 'hidden md:block']">
                        <ul class="mt-4 flex flex-col space-y-2 md:mt-0 md:flex-row md:space-y-0 md:space-x-4">
                            <li><Link :href="route('shop.products.index')" class="hover:text-gray-200">Products</Link></li>
                            <li>
                                <Link :href="route('cart.index')" class="hover:text-gray-200">Cart ({{ cartCount }})</Link>
                            </li>
                            <li v-if="authUser">
                                <Link :href="route('profile')" class="hover:text-gray-200">My Account</Link>
                            </li>
                            <li v-if="authUser">
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

        <!-- Main Content -->
        <main class="container mx-auto flex-1 px-4 py-6">
            <slot></slot>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 py-8 text-white">
            <div class="container mx-auto grid grid-cols-1 gap-8 px-4 md:grid-cols-3">
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
            <div class="mt-6 text-center text-sm text-gray-400">&copy; 2025 E-Commerce Shop. All rights reserved.</div>
        </footer>
    </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const page = usePage();
const authUser = page.props.auth?.user;
console.log(page.props);
const categories = ref([]);
const cartCount = ref(0);
const isMenuOpen = ref(false);

onMounted(() => {
    categories.value = page.props.categories || [];
    cartCount.value = page.props.cartCount || 0;
});

function logout() {
    router.post(route('logout'));
}
</script>
