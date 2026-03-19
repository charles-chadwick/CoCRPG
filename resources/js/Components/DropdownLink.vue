<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-darker-300 transition duration-150 ease-in-out hover:bg-darker-700 hover:text-darker-100 focus:bg-darker-700 focus:outline-none"
    >
        <slot />
    </Link>
</template>
