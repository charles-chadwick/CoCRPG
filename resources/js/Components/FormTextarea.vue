<script setup>
defineProps({
    model_value: {
        type: String,
        default: '',
    },
    id: String,
    name: String,
    placeholder: String,
    rows: {
        type: Number,
        default: 4,
    },
    disabled: Boolean,
    required: Boolean,
    error: String,
});

defineEmits(['update:model_value']);
</script>

<template>
    <div class="flex flex-col gap-1">
        <textarea
            :id="id"
            :name="name"
            :rows="rows"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            :class="[
                'w-full rounded-lg border px-3 py-2 bg-white text-darker-900 shadow-xs transition',
                'placeholder:text-darker-400 resize-y',
                'focus:outline-none focus:ring-2 focus:ring-offset-0',
                'disabled:cursor-not-allowed disabled:bg-darker-100 disabled:text-darker-400',
                error
                    ? 'border-secondary-400 focus:border-secondary-500 focus:ring-secondary-200'
                    : 'border-darker-300 focus:border-primary-500 focus:ring-primary-200',
            ]"
            @input="$emit('update:model_value', $event.target.value)"
        >{{ model_value }}</textarea>
        <p v-if="error" class="text-xs text-secondary-500">{{ error }}</p>
    </div>
</template>
