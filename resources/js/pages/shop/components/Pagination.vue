<template>
    <div v-if="links.length > 3" class="mt-4 flex justify-center">
        <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
            <template v-for="(link, index) in links" :key="index">
                <component
                    :is="link.url ? Link : 'span'"
                    :href="link.url"
                    v-html="link.label"
                    class="border px-3 py-1 text-sm transition duration-150 ease-in-out"
                    :class="[
                        link.active ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100',
                        !link.url && 'cursor-not-allowed text-gray-400',
                    ]"
                />
            </template>
        </nav>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>
