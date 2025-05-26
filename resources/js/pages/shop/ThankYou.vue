<template>
    <shop-layout>
        <div class="container mx-auto px-4 py-8">
            <div class="mx-auto max-w-2xl rounded-2xl bg-white p-6 text-center shadow-md sm:p-8">
                <!-- Success Icon & Title -->
                <div class="mb-6">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <h1 class="mb-2 text-2xl font-bold sm:text-3xl">Terima Kasih atas Pesanan Anda!</h1>
                    <p class="text-gray-600">Pesanan Anda telah berhasil dibuat.</p>
                </div>
                <!-- Order Details -->
                <div class="mb-6 rounded-lg border bg-gray-50 p-4 text-left">
                    <h2 class="mb-3 text-lg font-semibold sm:text-xl">Detail Pesanan</h2>
                    <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div class="text-gray-600">Nomor Pesanan:</div>
                        <div class="font-medium">{{ order.order_number }}</div>
                        <div class="text-gray-600">Tanggal:</div>
                        <div class="font-medium">{{ formatDate(order.created_at) }}</div>
                        <div class="text-gray-600">Total Pembayaran:</div>
                        <div class="font-medium">Rp{{ formatPrice(order.total_amount) }}</div>
                        <div class="text-gray-600">Metode Pembayaran:</div>
                        <div class="font-medium">{{ formatPaymentMethod(order.payment_method) }}</div>
                        <div class="text-gray-600">Status:</div>
                        <div class="font-medium">
                            <span class="inline-block rounded px-2 py-1 text-xs" :class="statusClass(order.status)">
                                {{ order.status }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- What's Next -->
                <div class="mb-6 text-left">
                    <h2 class="mb-2 text-lg font-semibold sm:text-xl">Langkah Selanjutnya</h2>
                    <p class="text-sm text-gray-600">
                        Anda akan menerima email konfirmasi ke alamat
                        <span class="font-medium">{{ $page.props.auth.user.email }}</span
                        >. Kami akan mengirimkan notifikasi ketika pesanan Anda telah dikirim.
                    </p>
                </div>
                <!-- Buttons -->
                <div class="flex flex-col justify-center gap-4 sm:flex-row">
                    <Link :href="route('home')" class="rounded bg-blue-600 px-6 py-2 text-center text-sm text-white hover:bg-blue-700">
                        Lanjut Belanja
                    </Link>
                    <Link :href="route('myorders.index')" class="rounded bg-gray-600 px-6 py-2 text-center text-sm text-white hover:bg-gray-700">
                        Lihat Riwayat Pesanan
                    </Link>
                </div>
            </div>
        </div>
    </shop-layout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ShopLayout from '../shop/ShopApp.vue';

defineProps({
    order: Object,
});

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}
function formatPrice(price) {
    // Convert to number and remove decimal part
    const wholeNumber = Math.floor(Number(price));

    // Format with dot as thousand separator
    return wholeNumber.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function formatPaymentMethod(method) {
    const methods = {
        credit_card: 'Credit Card',
        paypal: 'PayPal',
        bank_transfer: 'Bank Transfer',
    };
    return methods[method] || method;
}

function statusClass(status) {
    return (
        {
            pending: 'bg-yellow-100 text-yellow-800',
            processing: 'bg-blue-100 text-blue-800',
            completed: 'bg-green-100 text-green-800',
            cancelled: 'bg-red-100 text-red-800',
        }[status] || 'bg-gray-100 text-gray-800'
    );
}
</script>
