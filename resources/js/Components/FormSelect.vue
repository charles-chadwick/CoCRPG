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
                'w-full rounded-lg border px-3 py-2 text-sm text-darker-900 shadow-xs transition',
                'focus:outline-none focus:ring-2 focus:ring-offset-0',
                'disabled:cursor-not-allowed disabled:bg-darker-100 disabled:text-darker-400',
                error
                    ? 'border-secondary-400 focus:border-secondary-500 focus:ring-secondary-200'
                    : 'border-darker-300 focus:border-primary-500 focus:ring-primary-200',
            ]"
            :value="model_value"
            @change="$emit('update:modelValue', $event.target.value)"
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
        <p v-if="error" class="text-xs text-secondary-500">{{ error }}</p>
    </div>
</template>
