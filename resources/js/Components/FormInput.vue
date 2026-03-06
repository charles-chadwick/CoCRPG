<script setup>
defineProps({
    type: {
        type: String,
        default: 'text',
    },
    model_value: {
        type: [String, Number],
        default: '',
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
        <input
            :id="id"
            :name="name"
            :type="type"
            :value="model_value"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            :class="[
                'w-full rounded-lg border px-3 py-2 bg-white text-darker-900 shadow-xs transition',
                'placeholder:text-darker-400',
                'focus:outline-none focus:ring-2 focus:ring-offset-0',
                'disabled:cursor-not-allowed disabled:bg-darker-100 disabled:text-darker-400',
                error
                    ? 'border-secondary-400 focus:border-secondary-500 focus:ring-secondary-200'
                    : 'border-darker-300 focus:border-primary-500 focus:ring-primary-200',
            ]"
            @input="$emit('update:model_value', $event.target.value)"
        />
        <p v-if="error" class="text-xs text-secondary-500">{{ error }}</p>
    </div>
</template>
