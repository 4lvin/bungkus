<template>
    <shop-layout>
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col gap-6 md:flex-row">
                <!-- Sidebar -->
                <div class="w-full md:w-1/4">
                    <div class="overflow-hidden rounded-lg bg-white shadow">
                        <div class="bg-blue-600 p-6 text-white">
                            <h2 class="text-xl font-semibold">{{ user.name }}</h2>
                            <p class="text-blue-100">{{ user.email }}</p>
                        </div>

                        <div class="p-4">
                            <nav>
                                <ul class="space-y-2">
                                    <li>
                                        <a
                                            href="#account-info"
                                            class="block rounded px-3 py-2 hover:bg-blue-50"
                                            :class="{ 'bg-blue-50 text-blue-600': activeTab === 'account-info' }"
                                            @click.prevent="activeTab = 'account-info'"
                                        >
                                            Account Information
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="#orders"
                                            class="block rounded px-3 py-2 hover:bg-blue-50"
                                            :class="{ 'bg-blue-50 text-blue-600': activeTab === 'orders' }"
                                            @click.prevent="activeTab = 'orders'"
                                        >
                                            Order History
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="#addresses"
                                            class="block rounded px-3 py-2 hover:bg-blue-50"
                                            :class="{ 'bg-blue-50 text-blue-600': activeTab === 'addresses' }"
                                            @click.prevent="activeTab = 'addresses'"
                                        >
                                            Saved Addresses
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="w-full md:w-3/4">
                    <!-- Account Information -->
                    <div v-if="activeTab === 'account-info'" class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-4 text-xl font-semibold">Account Information</h2>

                        <form @submit.prevent="updateAccount">
                            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                                    <input
                                        type="text"
                                        id="name"
                                        v-model="accountForm.name"
                                        class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email Address</label>
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="accountForm.email"
                                        class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                    />
                                </div>
                            </div>

                            <h3 class="mb-3 text-lg font-medium">Change Password</h3>
                            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label for="current_password" class="mb-1 block text-sm font-medium text-gray-700">Current Password</label>
                                    <input
                                        type="password"
                                        id="current_password"
                                        v-model="accountForm.current_password"
                                        class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                    />
                                </div>

                                <div></div>

                                <div>
                                    <label for="password" class="mb-1 block text-sm font-medium text-gray-700">New Password</label>
                                    <input
                                        type="password"
                                        id="password"
                                        v-model="accountForm.password"
                                        class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700"
                                        >Confirm New Password</label
                                    >
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        v-model="accountForm.password_confirmation"
                                        class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                                    :disabled="accountProcessing"
                                >
                                    {{ accountProcessing ? 'Updating...' : 'Update Account' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Order History -->
                    <div v-if="activeTab === 'orders'" class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-4 text-xl font-semibold">Order History</h2>

                        <div v-if="orders.length === 0" class="text-gray-600">You haven't placed any orders yet.</div>

                        <div v-else class="space-y-4">
                            <div v-for="order in orders" :key="order.id" class="overflow-hidden rounded-lg border">
                                <div class="flex items-center justify-between bg-gray-50 p-4">
                                    <div>
                                        <h3 class="font-medium">Order #{{ order.order_number }}</h3>
                                        <p class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</p>
                                    </div>
                                    <div>
                                        <span
                                            class="rounded px-2 py-1 text-xs"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                                'bg-blue-100 text-blue-800': order.status === 'processing',
                                                'bg-green-100 text-green-800': order.status === 'completed',
                                                'bg-red-100 text-red-800': order.status === 'cancelled',
                                            }"
                                        >
                                            {{ order.status }}
                                        </span>
                                    </div>
                                </div>

                                <div class="border-t p-4">
                                    <div v-for="item in order.items" :key="item.id" class="border-b py-2 last:border-b-0">
                                        <div class="flex justify-between">
                                            <div>
                                                <p class="font-medium">{{ item.product_name }}</p>
                                                <p class="text-sm text-gray-600">Qty: {{ item.quantity }} × ${{ item.price }}</p>
                                            </div>
                                            <p class="font-medium">${{ (item.price * item.quantity).toFixed(2) }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-between font-semibold">
                                        <span>Total</span>
                                        <span>${{ order.total_amount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Saved Addresses -->
                    <div v-if="activeTab === 'addresses'" class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-4 text-xl font-semibold">Saved Addresses</h2>

                        <div v-if="addresses.length === 0" class="mb-4 text-gray-600">You don't have any saved addresses yet.</div>

                        <div v-else class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div v-for="(address, index) in addresses" :key="index" class="rounded-lg border p-4">
                                <div class="mb-2 flex justify-between">
                                    <h3 class="font-medium">{{ address.label || 'Address ' + (index + 1) }}</h3>
                                    <div class="flex space-x-2">
                                        <button @click="editAddress(index)" class="text-blue-600 hover:text-blue-800">Edit</button>
                                        <button @click="deleteAddress(index)" class="text-red-600 hover:text-red-800">Delete</button>
                                    </div>
                                </div>

                                <div class="text-gray-600">
                                    <p>{{ address.street }}</p>
                                    <p>{{ address.city }}, {{ address.state }} {{ address.zipcode }}</p>
                                    <p>{{ address.country }}</p>
                                    <p v-if="address.phone">Phone: {{ address.phone }}</p>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="showAddressForm = true"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                            v-if="!showAddressForm"
                        >
                            Add New Address
                        </button>

                        <div v-if="showAddressForm" class="mt-6 border-t pt-4">
                            <h3 class="mb-3 text-lg font-medium">{{ editingAddressIndex === null ? 'Add New Address' : 'Edit Address' }}</h3>

                            <form @submit.prevent="saveAddress">
                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="address_label" class="mb-1 block text-sm font-medium text-gray-700"
                                            >Label (e.g. Home, Work)</label
                                        >
                                        <input
                                            type="text"
                                            id="address_label"
                                            v-model="addressForm.label"
                                            class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label for="address_street" class="mb-1 block text-sm font-medium text-gray-700">Street Address</label>
                                        <input
                                            type="text"
                                            id="address_street"
                                            v-model="addressForm.street"
                                            class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label for="address_city" class="mb-1 block text-sm font-medium text-gray-700">City</label>
                                        <input
                                            type="text"
                                            id="address_city"
                                            v-model="addressForm.city"
                                            class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label for="address_state" class="mb-1 block text-sm font-medium text-gray-700">State/Province</label>
                                        <input
                                            type="text"
                                            id="address_state"
                                            v-model="addressForm.state"
                                            class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label for="address_zipcode" class="mb-1 block text-sm font-medium text-gray-700">ZIP/Postal Code</label>
                                        <input
                                            type="text"
                                            id="address_zipcode"
                                            v-model="addressForm.zipcode"
                                            class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label for="address_country" class="mb-1 block text-sm font-medium text-gray-700">Country</label>
                                        <input
                                            type="text"
                                            id="address_country"
                                            v-model="addressForm.country"
                                            class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label for="address_phone" class="mb-1 block text-sm font-medium text-gray-700">Phone (Optional)</label>
                                        <input
                                            type="tel"
                                            id="address_phone"
                                            v-model="addressForm.phone"
                                            class="focus:ring-opacity-50 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500"
                                        />
                                    </div>
                                </div>

                                <div class="flex space-x-3">
                                    <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                                        {{ editingAddressIndex === null ? 'Add Address' : 'Update Address' }}
                                    </button>

                                    <button
                                        type="button"
                                        @click="cancelAddressForm"
                                        class="rounded bg-gray-600 px-4 py-2 text-white hover:bg-gray-700"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </shop-layout>
</template>

<script>
import ShopLayout from '../ShopApp.vue';

export default {
    components: {
        ShopLayout,
    },
    props: {
        user: Object,
        orders: Array,
    },
    data() {
        return {
            activeTab: 'account-info',
            accountForm: {
                name: this.user.name,
                email: this.user.email,
                current_password: '',
                password: '',
                password_confirmation: '',
            },
            accountProcessing: false,
            addresses: [], // This would be loaded from the user's saved addresses
            showAddressForm: false,
            editingAddressIndex: null,
            addressForm: {
                label: '',
                street: '',
                city: '',
                state: '',
                zipcode: '',
                country: '',
                phone: '',
            },
        };
    },
    methods: {
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        },
        updateAccount() {
            this.accountProcessing = true;

            // This would send a request to update the account information
            this.$inertia.patch(route('profile.update'), this.accountForm, {
                onSuccess: () => {
                    this.accountProcessing = false;
                    this.accountForm.current_password = '';
                    this.accountForm.password = '';
                    this.accountForm.password_confirmation = '';
                },
                onError: () => {
                    this.accountProcessing = false;
                },
            });
        },
        editAddress(index) {
            this.editingAddressIndex = index;
            this.addressForm = { ...this.addresses[index] };
            this.showAddressForm = true;
        },
        deleteAddress(index) {
            if (confirm('Are you sure you want to delete this address?')) {
                this.addresses.splice(index, 1);
                // This would send a request to delete the address
            }
        },
        saveAddress() {
            if (this.editingAddressIndex === null) {
                // Add new address
                this.addresses.push({ ...this.addressForm });
            } else {
                // Update existing address
                this.addresses[this.editingAddressIndex] = { ...this.addressForm };
            }

            // This would send a request to save the address

            this.cancelAddressForm();
        },
        cancelAddressForm() {
            this.showAddressForm = false;
            this.editingAddressIndex = null;
            this.addressForm = {
                label: '',
                street: '',
                city: '',
                state: '',
                zipcode: '',
                country: '',
                phone: '',
            };
        },
    },
};
</script>
