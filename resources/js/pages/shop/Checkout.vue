<template>
    <shop-layout>
        <div class="container mx-auto px-4">
            <h1 class="mb-6 text-2xl font-bold">Checkout</h1>

            <div class="flex flex-col gap-6 lg:flex-row">
                <!-- Checkout Form -->
                <div class="w-full lg:w-2/3">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-4 text-xl font-semibold">Shipping Information</h2>

                        <form @submit.prevent="submitOrder">
                            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                                <template v-for="field in fields" :key="field.name">
                                    <div :class="{ 'md:col-span-2': field.fullWidth }">
                                        <label :for="field.name" class="mb-1 block text-sm font-medium text-gray-700">
                                            {{ field.label }}
                                        </label>
                                        <input
                                            :type="field.type"
                                            :id="field.name"
                                            v-model="form[field.name]"
                                            class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                            :required="field.required"
                                        />
                                        <div v-if="form.errors[field.name]" class="mt-1 text-sm text-red-500">
                                            {{ form.errors[field.name] }}
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-6">
                                <h2 class="mb-4 text-xl font-semibold">Payment Method</h2>
                                <div class="space-y-3">
                                    <label v-for="method in paymentMethods" :key="method.value" class="flex items-center">
                                        <input type="radio" v-model="form.payment_method" :value="method.value" class="mr-2" required />
                                        {{ method.label }}
                                    </label>
                                </div>
                                <div v-if="errors.payment_method" class="mt-1 text-sm text-red-500">{{ errors.payment_method }}</div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-6">
                                <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">Order Notes (Optional)</label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                ></textarea>
                                <div v-if="errors.notes" class="mt-1 text-sm text-red-500">{{ errors.notes }}</div>
                            </div>

                            <!-- Submit -->
                            <div>
                                <button
                                    type="submit"
                                    class="w-full rounded bg-blue-600 px-4 py-3 font-semibold text-white hover:bg-blue-700"
                                    :disabled="processing"
                                >
                                    {{ processing ? 'Processing...' : 'Place Order' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="w-full lg:w-1/3">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-4 text-xl font-semibold">Order Summary</h2>

                        <div class="mb-4">
                            <h3 class="mb-2 font-medium">Order Items ({{ props.cartItems.length }})</h3>
                            <div v-for="item in props.cartItems" :key="item.id" class="flex justify-between border-b py-2">
                                <div>
                                    <p class="font-medium">{{ item.name }}</p>
                                    <p class="text-sm text-gray-600">Qty: {{ item.quantity }}</p>
                                </div>
                                <p class="font-medium">${{ (item.price * item.quantity).toFixed(2) }}</p>
                            </div>
                        </div>

                        <div class="mb-4 border-b pb-4">
                            <div class="mb-2 flex justify-between">
                                <span>Subtotal</span>
                                <span>${{ subtotal.toFixed(2) }}</span>
                            </div>
                            <div class="mb-2 flex justify-between">
                                <span>Tax ({{ taxRate * 100 }}%)</span>
                                <span>${{ tax.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Shipping</span>
                                <span>${{ shipping.toFixed(2) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total</span>
                            <span>${{ total.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </shop-layout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';
import ShopLayout from '../shop/ShopApp.vue';

const props = defineProps({
    cartItems: Array,
    errors: Object,
});

const user = usePage().props.auth.user;

const form = useForm({
    shipping_fullname: user?.name || '',
    shipping_address: '',
    shipping_city: '',
    shipping_state: '',
    shipping_zipcode: '',
    shipping_phone: '',
    payment_method: 'credit_card',
    notes: '',
});

const taxRate = 0.07;
const shipping = 5.0;
const processing = ref(false);

const subtotal = computed(() => props.cartItems.reduce((total, item) => total + item.price * item.quantity, 0));
const tax = computed(() => subtotal.value * taxRate);
const total = computed(() => subtotal.value + tax.value + shipping);

// Check if SweetAlert2 is loaded
onMounted(() => {
    if (typeof Swal === 'undefined') {
        // Add SweetAlert2 CDN if not available
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(script);
    }
});

const submitOrder = () => {
    processing.value = true;
    const payload = {
        ...form,
        total_amount: total.value,
    };

    form.post(route('checkout.store'), {
        onSuccess: () => {
            processing.value = false;
            Swal.fire({
                title: 'Success!',
                text: 'Your order has been placed successfully!',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3B82F6',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to order confirmation or home page
                    window.location.href = route('home');
                }
            });
        },
        onError: (errors) => {
            processing.value = false;
            let errorMessage = 'There was a problem processing your order.';

            // If we have specific errors, show the first one
            if (Object.keys(errors).length > 0) {
                const firstError = Object.values(errors)[0];
                errorMessage = firstError || errorMessage;
            }

            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3B82F6',
            });
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};

const fields = [
    { name: 'shipping_fullname', label: 'Full Name', type: 'text', required: true },
    { name: 'shipping_phone', label: 'Phone Number', type: 'tel', required: true },
    { name: 'shipping_address', label: 'Address', type: 'text', required: true, fullWidth: true },
    { name: 'shipping_city', label: 'City', type: 'text', required: true },
    { name: 'shipping_state', label: 'State/Province', type: 'text', required: true },
    { name: 'shipping_zipcode', label: 'ZIP/Postal Code', type: 'text', required: true },
];

const paymentMethods = [
    { value: 'credit_card', label: 'Credit Card' },
    { value: 'paypal', label: 'PayPal' },
    { value: 'bank_transfer', label: 'Bank Transfer' },
];
</script>
