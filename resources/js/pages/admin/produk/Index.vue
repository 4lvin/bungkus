<template>
    <AppLayout title="Products">
        <div class="p-4">
            <Card>
                <template #title>Produk</template>
                <template #content>
                    <Button label="Tambah Produk" class="mt-4" icon="pi pi-plus" @click="onClickAdd" />
                    <DataTable
                        :value="products.data"
                        :lazy="true"
                        :paginator="true"
                        scrollable
                        scrollHeight="600px"
                        :rows="products.per_page"
                        :totalRecords="products.total"
                        :first="(products.current_page - 1) * products.per_page"
                        @page="onPage"
                        dataKey="id"
                    >
                        <Column field="name" header="Nama" />
                        <Column field="category.name" header="Kategori" />
                        <Column field="price" header="Harga" />
                        <Column field="quantity" header="Stok" />
                        <Column field="is_active" header="Status">
                            <template #body="{ data }">
                                <Tag :value="data.is_active ? 'Aktif' : 'Nonaktif'" :severity="data.is_active ? 'success' : 'danger'" />
                            </template>
                        </Column>
                        <Column header="Aksi">
                            <template #body="{ data }">
                                <Button icon="pi pi-pencil" class="p-button-text" @click="onClickAdd(data)" />
                                <Button icon="pi pi-trash" class="p-button-text p-button-danger" @click="onClickDelete(data)" />
                            </template>
                        </Column>
                    </DataTable>
                    <div class="mt-2 flex flex-col items-center justify-between gap-2 text-sm text-gray-600 md:flex-row">
                        <div>
                            Menampilkan <strong>{{ fromItem }}–{{ toItem }}</strong> dari <strong>{{ products.total }}</strong> data |
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

    <Dialog v-model:visible="visible" :header="labelHeader" modal :style="{ width: '40rem' }">
        <AddProduct
            :data="dataProduk"
            :categories="props.categories"
            :onClose="onHideForm"
            @on-change-loading="onChangeLoading"
            :is-loading="isLoading"
        />
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
import AddProduct from './AddProduct.vue'; // komponen form dialog

const props = defineProps({ products: Object, categories: Array });

const visible = ref(false);
const labelHeader = ref('Tambah Produk');
const dataProduk = ref();
const isLoading = ref(false);
const confirmDelete = useConfirm();
const toast = useToast();
const page = usePage();

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

function onClickAdd(data: any = null) {
    visible.value = true;
    dataProduk.value = data;
    labelHeader.value = data?.id ? 'Edit Produk' : 'Tambah Produk';
}

function onHideForm() {
    visible.value = false;
}

function onChangeLoading(val: boolean) {
    isLoading.value = val;
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
    router.delete(route('admin.products.destroy', dataProduk.value.id), {
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

const perPage = ref(props.products.per_page);
const currentPage = ref(props.products.current_page);

// Hitung item yang ditampilkan
const fromItem = computed(() => (props.products.current_page - 1) * props.products.per_page + 1);
const toItem = computed(() => {
    const end = props.products.current_page * props.products.per_page;
    return end > props.products.total ? props.products.total : end;
});

// Pagination handler
const onPage = (event: any) => {
    router.get(
        route('admin.products.index'),
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
        route('admin.products.index'),
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
