<script setup>
defineProps({
    model_value: {
        type: [String, Number],
        default: '',
    },
    options: {
        /** @type {{ value: string|number, label: string }[]} */
        type: Array,
        default: () => [],
    },
    id: String,
    name: String,
    placeholder: String,
    disabled: Boolean,
    required: Boolean,
    error: String,
});

defineEmits(['update:model_value']);
</script>

<template>
    <div class="flex flex-col gap-1">
        <select
            :id="id"
            :name="name"
            :disabled="disabled"
            :required="required"
            :class="[
                'w-full rounded-lg border px-3 py-2 bg-darker-800 text-darker-100 shadow-xs transition',
                'focus:outline-none focus:ring-2 focus:ring-offset-0',
                'disabled:cursor-not-allowed disabled:bg-darker-900 disabled:text-darker-500',
                error
                    ? 'border-red-500 focus:border-red-400 focus:ring-red-500/30'
                    : 'border-darker-700 focus:border-primary-500 focus:ring-primary-500/30',
            ]"
            :value="model_value"
            @change="$emit('update:model_value', $event.target.value)"
        >
            <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
        <p v-if="error" class="text-xs text-red-400">{{ error }}</p>
    </div>
</template>
