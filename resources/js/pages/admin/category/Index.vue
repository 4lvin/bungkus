<template>
    <AppLayout title="Categories">
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
                    <Button icon="pi pi-pencil" class="mr-2" @click="editCategory(data.id)" />
                    <Button icon="pi pi-trash" severity="danger" @click="deleteCategory(data.id)" />
                </template>
            </Column>
        </DataTable>

        <Button label="Add New" class="mt-4" icon="pi pi-plus" @click="goToCreate" />
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

defineProps({ categories: Object });

const goToCreate = () => router.visit(route('admin.categories.create'));
const editCategory = (id) => router.visit(route('admin.categories.edit', id));
const deleteCategory = (id) => {
    if (confirm('Yakin ingin menghapus kategori ini?')) {
        router.delete(route('admin.categories.destroy', id));
    }
};
</script>
