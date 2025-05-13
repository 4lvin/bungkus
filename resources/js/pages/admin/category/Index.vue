<template>
    <AppLayout title="Categories">
        <div class="p-4">
            <Card>
                <template #title>Kategori</template>
                <template #content>
                    <Button label="Tambah Kategori" class="mt-4" icon="pi pi-plus" @click="onClickAdd" />
                    <DataTable
                        :value="categories.data"
                        paginator
                        :rows="10"
                        :totalRecords="categories.total"
                        lazy
                        :first="(categories.current_page - 1) * categories.per_page"
                    >
                        <Column field="name" header="Name" />
                        <Column field="description" header="Description" />
                        <Column field="is_active" header="Active">
                            <template #body="{ data }">
                                <Tag :value="data.is_active ? 'Yes' : 'No'" :severity="data.is_active ? 'success' : 'danger'" />
                            </template>
                        </Column>
                        <Column header="Actions">
                            <template #body="{ data }">
                                <Button icon="pi pi-pencil" class="p-button-text" @click="onClickAdd(data)" />
                                <Button icon="pi pi-trash" class="p-button-text p-button-danger" @click="onClickDelete(data)" />
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
        </div>
    </AppLayout>
    <Dialog v-model:visible="visible" :header="labelHeader" modal :style="{ width: '40rem' }">
        <AddCategory :data="dataKategori" :onClose="onHideForm" @on-change-loading="onChangeLoading" :is-loading="isLoading" />
    </Dialog>
    <ConfirmDialog
        :draggable="false"
        group="delete-group"
        :pt="{
            pcCloseButton: {
                root: {
                    class: 'hidden',
                },
            },
            accept: {
                label: 'Ya',
            },
        }"
    >
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="p-confirmdialog">
                <div class="p-dialog-header">
                    <span class="p-dialog-title">{{ message.header }}</span>
                </div>
                <div class="p-dialog-content">
                    <span class="p-confirmdialog-message">
                        {{ message.message }}
                        <strong class="ml-1">Kategori:</strong>
                        <strong class="ml-1">
                            {{ dataKategori.name }}
                        </strong>
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
import { ref, watch } from 'vue';
import AddCategory from './AddCategory.vue';

const props = defineProps({ categories: Object });
const visible = ref(false);
const labelHeader = ref('Tambah Kategori');
const dataKategori = ref();
const isLoading = ref(false);
const page = usePage();
const toast = useToast();
const confirmDelete = useConfirm();
watch(
    () => page.props.flash as FlashMessage,
    (flash) => {
        if (flash.success) {
            toast.add({
                severity: 'success',
                summary: 'Berhasil',
                detail: flash.success,
                life: 3000,
            });
        }
        if (flash.error) {
            toast.add({
                severity: 'error',
                summary: 'Gagal',
                detail: flash.error,
                life: 3000,
            });
        }
    },
);
function onClickAdd(data: unknown) {
    visible.value = true;
    dataKategori.value = data;
    if (data.id == null) {
        labelHeader.value = 'Tambah Kategori';
    } else {
        labelHeader.value = 'Edit Kategori';
    }
}
const onChangeLoading = (loadingValue: boolean) => {
    isLoading.value = loadingValue;
};

function onHideForm() {
    visible.value = false;
}
function onClickDelete(data: unknown) {
    dataKategori.value = data;
    openDialogDelete();
}

const openDialogDelete = () => {
    confirmDelete.require({
        group: 'delete-group',
        message: `Apakah anda yakin untuk menghapus produk ini?`,
        header: 'Konfirmasi',
    });
};

const handleAccept = (accept: unknown) => {
    deleteTask(accept);
};

const deleteTask = (accept: any) => {
    isLoading.value = true;
    router.delete(route('admin.categories.destroy', dataKategori.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isLoading.value = false;
            accept();
        },
        onError: () => {
            isLoading.value = false;
        },
    });
};

const handleReject = (reject: any) => {
    reject();
};
</script>
