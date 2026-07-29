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
                'w-full rounded-lg border px-3 py-2 bg-darker-800 text-darker-100 shadow-xs transition',
                'placeholder:text-darker-500',
                'focus:outline-none focus:ring-2 focus:ring-offset-0',
                'disabled:cursor-not-allowed disabled:bg-darker-900 disabled:text-darker-500',
                error
                    ? 'border-red-500 focus:border-red-400 focus:ring-red-500/30'
                    : 'border-darker-700 focus:border-primary-500 focus:ring-primary-500/30',
            ]"
            @input="$emit('update:model_value', $event.target.value)"
        />
        <p v-if="error" class="text-xs text-red-400">{{ error }}</p>
    </div>
</template>
