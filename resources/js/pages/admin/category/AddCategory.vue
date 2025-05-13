<template>
    <Form v-slot="$form" :initialValues="dataInput" :resolver="resolver" @submit="save" class="space-y-4 rounded-md bg-white p-4 shadow-md">
        <!-- Nama -->
        <div>
            <label class="mb-1 block text-sm font-medium">Nama</label>
            <InputText v-model="form.name" name="name" ref="nameInput" class="w-full" />
            <Message v-if="$form.name?.invalid" severity="error" size="small" variant="simple">
                {{ $form.name.error?.message }}
            </Message>
        </div>

        <!-- Deskripsi -->
        <div>
            <label class="mb-1 block text-sm font-medium">Deskripsi</label>
            <InputText v-model="form.description" name="description" class="w-full" />
            <Message v-if="$form.description?.invalid" severity="error" size="small" variant="simple">
                {{ $form.description.error?.message }}
            </Message>
        </div>

        <!-- Aktif -->
        <div>
            <label class="mb-1 block text-sm font-medium">Status Aktif</label>
            <InputSwitch v-model="form.is_active" />
        </div>

        <!-- Gambar -->
        <div>
            <label class="mb-1 block text-sm font-medium">Upload Gambar</label>
            <input type="file" @change="handleFileChange" class="block w-full text-sm text-gray-700" />
        </div>

        <!-- Tombol Simpan -->
        <Button type="submit" :loading="isLoading" :label="isLoading ? 'Menyimpan Data' : 'Simpan'" icon="pi pi-check" class="w-full" />
    </Form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Form } from '@primevue/forms';
import { yupResolver } from '@primevue/forms/resolvers/yup';
import { ref } from 'vue';
import { object, string } from 'yup';

const props = defineProps({
    data: {
        type: Object,
        default: null,
    },
    onClose: {
        type: Function,
        default: () => ({}),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
});
const isLoading = ref(false);

const form = useForm({
    name: '' as string,
    description: '' as string,
    is_active: true as boolean,
    image: null,
});

const resolver = yupResolver(
    object().shape({
        name: string().required('Nama harus diisi'),
        description: string().required('description tidak boleh kosong'),
    }),
);
const handleFileChange = (e) => {
    form.image = e.target.files[0];
};
const dataInput = ref();
if (props.data) {
    dataInput.value = { ...dataInput.value, ...props.data };
    form.name = dataInput.value.name;
    form.description = dataInput.value.description;
}
const save = (data: any) => {
    if (data.valid) {
        isLoading.value = true;
        if (props.data.id) {
            form.put(route('admin.categories.update', props.data.id), {
                preserveScroll: true,
                onSuccess: () => {
                    isLoading.value = false;
                    props.onClose();
                },
                onError: () => {
                    isLoading.value = false;
                    props.onClose();
                },
            });
        } else {
            form.post(route('admin.categories.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    isLoading.value = false;
                    props.onClose();
                },
                onError: () => {
                    isLoading.value = false;
                    props.onClose();
                },
            });
        }
    }
};
</script>
