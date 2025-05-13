<template>
    <shop-layout>
        <div class="container mx-auto px-4 py-8">
            <h1 class="mb-8 border-b pb-4 text-3xl font-bold text-gray-800">Checkout</h1>

            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- Checkout Form -->
                <div class="w-full lg:w-2/3">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
                        <h2 class="mb-6 flex items-center text-2xl font-semibold text-gray-800">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mr-2 h-6 w-6 text-blue-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Shipping Information
                        </h2>

                        <form @submit.prevent="submitOrder" class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <template v-for="field in fields" :key="field.name">
                                    <div :class="{ 'md:col-span-2': field.fullWidth }" class="transition-all duration-200">
                                        <label :for="field.name" class="mb-2 block text-sm font-medium text-gray-700">
                                            {{ field.label }}
                                        </label>
                                        <input
                                            :type="field.type"
                                            :id="field.name"
                                            v-model="form[field.name]"
                                            class="focus:ring-opacity-50 w-full rounded-lg border-gray-300 shadow-sm transition-all duration-200 focus:border-blue-500 focus:ring focus:ring-blue-500"
                                            :required="field.required"
                                            :placeholder="'Enter your ' + field.label.toLowerCase()"
                                        />
                                        <div v-if="form.errors[field.name]" class="mt-2 text-sm text-red-500">
                                            {{ form.errors[field.name] }}
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Payment Method -->
                            <div class="border-t border-gray-100 pt-4">
                                <h2 class="mb-4 flex items-center text-2xl font-semibold text-gray-800">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="mr-2 h-6 w-6 text-blue-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                        />
                                    </svg>
                                    Payment Method
                                </h2>
                                <div class="mt-4 space-y-3 rounded-lg bg-gray-50 p-4">
                                    <label
                                        v-for="method in paymentMethods"
                                        :key="method.value"
                                        class="flex cursor-pointer items-center rounded-lg p-3 transition-all duration-200 hover:bg-gray-100"
                                    >
                                        <input
                                            type="radio"
                                            v-model="form.payment_method"
                                            :value="method.value"
                                            class="mr-3 h-5 w-5 text-blue-600"
                                            required
                                        />
                                        <span class="font-medium">{{ method.label }}</span>
                                    </label>
                                </div>
                                <div v-if="errors.payment_method" class="mt-2 text-sm text-red-500">{{ errors.payment_method }}</div>
                            </div>

                            <!-- Notes -->
                            <div class="border-t border-gray-100 pt-4">
                                <label for="notes" class="mb-2 block text-sm font-medium text-gray-700">Order Notes (Optional)</label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="focus:ring-opacity-50 w-full rounded-lg border-gray-300 shadow-sm transition-all duration-200 focus:border-blue-500 focus:ring focus:ring-blue-500"
                                    placeholder="Add any special instructions or notes about your order"
                                ></textarea>
                                <div v-if="errors.notes" class="mt-2 text-sm text-red-500">{{ errors.notes }}</div>
                            </div>

                            <!-- Submit -->
                            <div class="pt-6">
                                <button
                                    type="submit"
                                    class="w-full transform rounded-lg bg-blue-600 px-6 py-4 font-semibold text-white shadow-md transition-all duration-200 hover:-translate-y-1 hover:bg-blue-700 hover:shadow-lg"
                                    :disabled="processing"
                                >
                                    <span v-if="processing" class="flex items-center justify-center">
                                        <svg
                                            class="mr-3 -ml-1 h-5 w-5 animate-spin text-white"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        Processing...
                                    </span>
                                    <span v-else>Place Order</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mt-8 w-full lg:mt-0 lg:w-1/3">
                    <div class="sticky top-8 rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
                        <h2 class="mb-4 flex items-center text-2xl font-semibold text-gray-800">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mr-2 h-6 w-6 text-blue-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg>
                            Order Summary
                        </h2>

                        <div class="mb-6">
                            <h3 class="mb-3 flex items-center font-medium text-gray-700">
                                Order Items
                                <span class="ml-2 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800">{{
                                    props.cartItems.length
                                }}</span>
                            </h3>
                            <div class="max-h-60 space-y-3 overflow-y-auto pr-2">
                                <div v-for="item in props.cartItems" :key="item.id" class="flex items-center justify-between border-b pb-3">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800">{{ item.name }}</p>
                                        <p class="text-sm text-gray-600">Qty: {{ item.quantity }}</p>
                                    </div>
                                    <p class="font-medium text-gray-800">Rp {{ formatPrice(item.price * item.quantity) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 space-y-2 border-b pb-4">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal</span>
                                <span>Rp {{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Tax ({{ (taxRate * 100).toFixed(0) }}%)</span>
                                <span>Rp {{ formatPrice(tax) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Shipping</span>
                                <span>Rp {{ formatPrice(shipping) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between rounded-lg bg-gray-50 p-4 text-xl font-bold text-gray-800">
                            <span>Total</span>
                            <span>Rp {{ formatPrice(total) }}</span>
                        </div>

                        <div class="mt-6 text-center text-sm text-gray-500">
                            <p class="flex items-center justify-center">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mr-1 h-4 w-4 text-green-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    />
                                </svg>
                                Secure checkout
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </shop-layout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
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

// Format price function
function formatPrice(price) {
    // Convert to number and remove decimal part
    const wholeNumber = Math.floor(Number(price));

    // Format with dot as thousand separator
    return wholeNumber.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Check if SweetAlert2 is loaded
onMounted(() => {});

const submitOrder = () => {
    processing.value = true;
    const payload = {
        ...form,
        total_amount: total.value,
    };

    form.post(route('checkout.store'), {
        onSuccess: () => {
            processing.value = false;
        },
        onError: (errors) => {
            processing.value = false;
            let errorMessage = 'There was a problem processing your order.';

            // If we have specific errors, show the first one
            if (Object.keys(errors).length > 0) {
                const firstError = Object.values(errors)[0];
                errorMessage = firstError || errorMessage;
            }
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

<style scoped>
/* Custom scrollbar for order items */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
