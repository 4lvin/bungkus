<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '../shop/ShopApp.vue';

const props = defineProps({ orders: Object });
console.log(props.orders);
const perPage = ref(props.orders.per_page);
const onPage = (e: any) =>
    router.get(
        route('myorders.index'),
        { page: Math.floor(e.first / perPage.value) + 1, per_page: perPage.value },
        { preserveScroll: true, preserveState: true },
    );
const onPerPage = () => router.get(route('myorders.index'), { page: 1, per_page: perPage.value }, { preserveScroll: true, preserveState: true });

const fromItem = computed(() => (props.orders.current_page - 1) * props.orders.per_page + 1);
const toItem = computed(() => {
    const end = props.orders.current_page * props.orders.per_page;
    return end > props.orders.total ? props.orders.total : end;
});
</script>

<template>
    <AppLayout title="Pesanan Saya">
        <div class="space-y-4 p-4">
            <h1 class="text-xl font-semibold">Pesanan Saya</h1>

            <DataTable
                :value="orders.data"
                :paginator="true"
                :rows="orders.per_page"
                :totalRecords="orders.total"
                :first="(orders.current_page - 1) * orders.per_page"
                @page="onPage"
                scrollable
                scrollHeight="500px"
                dataKey="id"
            >
                <Column field="order_number" header="No Order" />
                <Column field="created_at" header="Tanggal">
                    <template #body="{ data }">
                        {{ new Date(data.created_at).toLocaleString('id-ID') }}
                    </template>
                </Column>
                <Column field="total_amount" header="Total">
                    <template #body="{ data }">
                        {{ Number(data.total_amount).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }) }}
                    </template>
                </Column>
                <Column field="status" header="Status">
                    <template #body="{ data }">
                        <Tag :value="data.status" :severity="data.status === 'success' ? 'success' : 'warn'" />
                    </template>
                </Column>
            </DataTable>

            <div class="mt-2 flex flex-col items-center justify-between gap-2 text-sm text-gray-600 md:flex-row">
                <div>
                    Menampilkan <strong>{{ fromItem }}–{{ toItem }}</strong> dari <strong>{{ orders.total }}</strong> data |
                    <strong>{{ perPage }}</strong> per halaman
                </div>
                <div class="flex items-center gap-2">
                    <span>Baris per halaman:</span>
                    <Dropdown :options="[5, 10, 20, 50]" v-model="perPage" @change="onPerPage" class="w-24" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
