<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-md border border-darker-600 bg-darker-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-darker-300 shadow-sm transition duration-150 ease-in-out hover:border-darker-500 hover:text-darker-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-darker-900 disabled:opacity-25"
    >
        <slot />
    </button>
</template>
