<template>
    <AppLayout title="Products">
        <div class="p-4">
            <Card>
                <template #title>Order</template>
                <template #content>
                    <!-- <Button label="Tambah Produk" class="mt-4" icon="pi pi-plus" @click="onClickAdd" /> -->
                    <DataTable
                        :value="orders.data"
                        :lazy="true"
                        :paginator="true"
                        scrollable
                        scrollHeight="600px"
                        :rows="orders.per_page"
                        :totalRecords="orders.total"
                        :first="(orders.current_page - 1) * orders.per_page"
                        @page="onPage"
                        dataKey="id"
                    >
                        <Column field="order_number" header="No Order" />
                        <Column field="shipping_fullname" header="Nama" />
                        <Column field="shipping_phone" header="Telp" />
                        <Column field="total_amount" header="Total pembelian" />
                        <Column field="status" header="Status">
                            <template #body="{ data }">
                                <Tag :value="data.status" :severity="data.status == 'success' ? 'success' : 'warn'" />
                            </template>
                        </Column>
                        <Column header="Aksi">
                            <template #body="{ data }">
                                <Button icon="pi pi-eye" class="p-button-text" @click="onClickDetail(data)" />
                                <!-- <Button icon="pi pi-pencil" class="p-button-text" @click="onClickAdd(data)" /> -->
                                <Button icon="pi pi-trash" class="p-button-text p-button-danger" @click="onClickDelete(data)" />
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
                            <Dropdown :options="[5, 10, 20, 50]" v-model="perPage" @change="onPageSizeChange" class="w-24" />
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </AppLayout>

    <Dialog v-model:visible="visible" :header="`Detail Order #${dataProduk?.order_number ?? ''}`" modal :style="{ width: '40rem' }">
        <DataTable :value="dataProduk.items" scrollable scrollHeight="300px" :rows="5">
            <Column field="product.name" header="Produk" />
            <Column field="price" header="Harga">
                <template #body="{ data }">{{ Number(data.price).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }) }}</template>
            </Column>
            <Column field="quantity" header="Qty" />
            <Column header="Subtotal">
                <template #body="{ data }">
                    {{ (Number(data.price) * data.quantity).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }) }}
                </template>
            </Column>
        </DataTable>
    </Dialog>

    <ConfirmDialog group="delete-group">
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="p-confirmdialog">
                <div class="p-dialog-header">
                    <span class="p-dialog-title">{{ message.header }}</span>
                </div>
                <div class="p-dialog-content">
                    <span class="p-confirmdialog-message">
                        {{ message.message }}
                        <strong class="ml-1">{{ dataProduk.name }}</strong>
                    </span>
                </div>
                <div class="p-dialog-footer">
                    <Button
                        :loading="isLoading"
                        :label="isLoading ? 'Menghapus Data' : 'Ya'"
                        class="p-confirmdialog-accept-button order-1"
                        type="button"
                        severity="success"
                        @click="handleAccept(acceptCallback)"
                    />
                    <Button
                        :disabled="isLoading"
                        label="Tidak"
                        class="p-confirmdialog-reject-button order-2"
                        type="button"
                        severity="danger"
                        @click="handleReject(rejectCallback)"
                    />
                </div>
            </div>
        </template>
    </ConfirmDialog>
    <Toast />
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { computed, ref, watch } from 'vue';

const props = defineProps({ orders: Object });

const visible = ref(false);
const labelHeader = ref('Tambah Produk');
const dataProduk = ref();
const isLoading = ref(false);
const confirmDelete = useConfirm();
const toast = useToast();
const page = usePage();
console.log(props.orders);
watch(
    () => page.props.flash,
    (flash) => {
        console.log(page.props.flash);
        if (flash.success) {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: flash.success, life: 3000 });
        }
        if (flash.error) {
            toast.add({ severity: 'error', summary: 'Gagal', detail: flash.error, life: 3000 });
        }
    },
);

function onClickDetail(data: any = null) {
    visible.value = true;
    dataProduk.value = data;
    labelHeader.value = data?.id ? 'Detail Produk' : 'Detail Produk';
}

function onClickDelete(data: any) {
    dataProduk.value = data;
    openDialogDelete();
}

function openDialogDelete() {
    confirmDelete.require({
        group: 'delete-group',
        message: 'Apakah anda yakin ingin menghapus produk ini?',
        header: 'Konfirmasi',
    });
}

function handleAccept(accept: any) {
    isLoading.value = true;
    router.delete(route('admin.orders.destroy', dataProduk.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isLoading.value = false;
            accept();
        },
        onError: () => {
            isLoading.value = false;
        },
    });
}

function handleReject(reject: any) {
    reject();
}

const perPage = ref(props.orders.per_page);
const currentPage = ref(props.orders.current_page);

// Hitung item yang ditampilkan
const fromItem = computed(() => (props.orders.current_page - 1) * props.orders.per_page + 1);
const toItem = computed(() => {
    const end = props.orders.current_page * props.orders.per_page;
    return end > props.orders.total ? props.orders.total : end;
});

// Pagination handler
const onPage = (event: any) => {
    router.get(
        route('admin.orders.index'),
        {
            page: Math.floor(event.first / perPage.value) + 1,
            per_page: perPage.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};

// PerPage handler
const onPageSizeChange = () => {
    router.get(
        route('admin.orders.index'),
        {
            page: 1,
            per_page: perPage.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};
</script>
