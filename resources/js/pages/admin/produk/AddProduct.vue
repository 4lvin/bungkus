<template>
    <Form v-slot="$form" :initialValues="dataInput" :resolver="resolver" @submit="save" class="space-y-4 rounded-md bg-white p-4 shadow-md">
        <!-- Nama Produk -->
        <div>
            <label class="mb-1 block text-sm font-medium">Nama Produk</label>
            <InputText v-model="form.name" name="name" class="w-full" />
            <Message v-if="$form.name?.invalid" severity="error" size="small" variant="simple">
                {{ $form.name.error?.message }}
            </Message>
        </div>

        <!-- Kategori -->
        <div>
            <label class="mb-1 block text-sm font-medium">Kategori</label>
            <Dropdown
                v-model="form.category_id"
                :options="categories"
                optionLabel="name"
                optionValue="id"
                placeholder="Pilih kategori"
                class="w-full"
            />
            <Message v-if="$form.category_id?.invalid" severity="error" size="small" variant="simple">
                {{ $form.category_id.error?.message }}
            </Message>
        </div>

        <!-- Harga -->
        <div>
            <label class="mb-1 block text-sm font-medium">Harga</label>
            <InputNumber v-model="form.price" name="price" class="w-full" inputClass="w-full" />
            <Message v-if="$form.price?.invalid" severity="error" size="small" variant="simple">
                {{ $form.price.error?.message }}
            </Message>
        </div>

        <!-- Jumlah -->
        <div>
            <label class="mb-1 block text-sm font-medium">Stok</label>
            <InputNumber v-model="form.quantity" name="quantity" class="w-full" inputClass="w-full" />
            <Message v-if="$form.quantity?.invalid" severity="error" size="small" variant="simple">
                {{ $form.quantity.error?.message }}
            </Message>
        </div>

        <!-- Deskripsi -->
        <div>
            <label class="mb-1 block text-sm font-medium">Deskripsi</label>
            <Textarea v-model="form.description" name="description" class="w-full" autoResize rows="3" />
            <Message v-if="$form.description?.invalid" severity="error" size="small" variant="simple">
                {{ $form.description.error?.message }}
            </Message>
        </div>

        <!-- Gambar -->
        <div>
            <label class="mb-1 block text-sm font-medium">Gambar Produk</label>
            <input type="file" @change="handleFileChange" class="w-full text-sm" />
            <div v-if="form.image_preview" class="mt-2">
                <img :src="form.image_preview" alt="Preview" class="h-24 rounded-md shadow" />
            </div>
        </div>

        <!-- Checkbox -->
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <Checkbox v-model="form.is_active" :binary="true" />
                <label>Status Aktif</label>
            </div>
            <div class="flex items-center gap-2">
                <Checkbox v-model="form.is_featured" :binary="true" />
                <label>Ditampilkan</label>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <Button
            type="submit"
            :loading="props.isLoading || isLoading"
            :label="props.isLoading ? 'Menyimpan Data...' : 'Simpan'"
            icon="pi pi-check"
            class="w-full"
        />
    </Form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Form } from '@primevue/forms';
import { yupResolver } from '@primevue/forms/resolvers/yup';
import { ref } from 'vue';
import { number, object, string } from 'yup';

const props = defineProps({
    categories: {
        type: Array,
        default: null,
    },
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
    name: '',
    category_id: null,
    price: null,
    quantity: null,
    description: '',
    image: null,
    is_active: true,
    is_featured: false,
    image_preview: null,
});

const resolver = yupResolver(
    object({
        name: string().required('Nama produk harus diisi'),
        category_id: number().required('Kategori harus dipilih'),
        price: number().min(0, 'Harga minimal 0').required('Harga harus diisi'),
        quantity: number().min(0, 'Stok minimal 0').required('Stok harus diisi'),
        description: string().nullable(),
    }),
);

const handleFileChange = (event: any) => {
    const file = event.target.files[0];
    form.image = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            form.image_preview = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const dataInput = ref({});
if (props.data) {
    dataInput.value = { ...props.data };
    form.name = dataInput.value.name;
    form.category_id = dataInput.value.category_id;
    form.price = dataInput.value.price;
    form.quantity = dataInput.value.quantity;
    form.description = dataInput.value.description;
    form.is_active = dataInput.value.is_active === 1;
    form.is_featured = dataInput.value.is_featured === 1;
    form.image_preview = dataInput.value.image || null;
}

const save = (data: any) => {
    if (data.valid) {
        isLoading.value = true;
        if (props.data.id) {
            form.put(route('admin.products.update', props.data.id), {
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
            form.post(route('admin.products.store'), {
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
