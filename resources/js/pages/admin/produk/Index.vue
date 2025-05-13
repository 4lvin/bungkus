<template>
    <AppLayout title="Categories">
        <div class="p-4">
            <Card>
                <template #title>Produk</template>
                <template #content>
                    <Button label="Tambah Produk" icon="pi pi-plus" class="mb-4" @click="$inertia.visit(route('admin.products.create'))" />
                    <DataTable :value="products.data" :paginator="true" :rows="10">
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
                                <Button icon="pi pi-pencil" class="p-button-text" @click="$inertia.visit(route('admin.products.edit', data.id))" />
                                <Button icon="pi pi-trash" class="p-button-text p-button-danger" @click="destroy(data.id)" />
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    products: Object,
});

function destroy(id) {
    if (confirm('Yakin hapus produk ini?')) {
        router.delete(route('admin.products.destroy', id));
    }
}
</script>
