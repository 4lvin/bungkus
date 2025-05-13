<template>
    <AppLayout :title="isEdit ? 'Edit Category' : 'Create Category'">
        <form @submit.prevent="submitForm" class="space-y-4">
            <InputText v-model="form.name" placeholder="Name" class="w-full" />
            <Textarea v-model="form.description" rows="3" placeholder="Description" class="w-full" />
            <InputSwitch v-model="form.is_active" />

            <input type="file" @change="handleFileChange" />

            <Button label="Save" icon="pi pi-check" />
        </form>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    category: Object,
    errors: Object,
});

const isEdit = !!props.category;
const form = useForm({
    name: props.category?.name ?? '',
    description: props.category?.description ?? '',
    is_active: props.category?.is_active ?? true,
    image: null,
});

const handleFileChange = (e) => {
    form.image = e.target.files[0];
};

const submitForm = () => {
    if (isEdit) {
        form.post(route('admin.categories.update', props.category.id), {
            forceFormData: true,
            preserveScroll: true,
            _method: 'put',
        });
    } else {
        form.post(route('admin.categories.store'), {
            forceFormData: true,
            preserveScroll: true,
        });
    }
};
</script>
