<script setup lang="ts">
import logo from '@/assets/logo_tumbas.png';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img :src="logo" alt="Logo" />
</template>
