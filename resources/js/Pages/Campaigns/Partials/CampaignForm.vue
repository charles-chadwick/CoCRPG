<script setup>
import { computed, ref } from 'vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormTextarea from '@/Components/FormTextarea.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    player_options: {
        type: Array,
        default: () => [],
    },
    session_status_options: {
        type: Array,
        default: () => [],
    },
    submit_label: {
        type: String,
        default: 'Save Campaign',
    },
});

defineEmits(['submit']);

const player_search = ref('');

const visible_player_options = computed(() => {
    const needle = player_search.value.trim().toLowerCase();

    if (needle === '') {
        return props.player_options;
    }

    return props.player_options.filter((option) => option.label.toLowerCase().includes(needle));
});

function isPlayerSelected(player_id) {
    return props.form.player_ids.includes(player_id);
}

function addSession() {
    props.form.sessions.push({
        id: null,
        title: '',
        scheduled_at: '',
        status: props.session_status_options[0]?.value ?? 'Scheduled',
        notes: '',
    });
}

function removeSession(index) {
    props.form.sessions.splice(index, 1);
}

const session_count = computed(() => props.form.sessions.length);
</script>

<template>
    <form class="space-y-6" @submit.prevent="$emit('submit')">
        <!-- Details -->
        <section class="rounded-lg border border-darker-700 bg-darker-900 p-6">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-primary-400">
                Details
            </h3>

            <div class="space-y-4">
                <div>
                    <InputLabel class="mb-1" for="title" value="Title" />
                    <FormInput
                        id="title"
                        v-model:model_value="form.title"
                        placeholder="Masks of Nyarlathotep"
                        :error="form.errors.title"
                        required
                    />
                </div>

                <div>
                    <InputLabel class="mb-1" for="description" value="Description" />
                    <FormTextarea
                        id="description"
                        v-model:model_value="form.description"
                        placeholder="What is this campaign about?"
                        :rows="5"
                        :error="form.errors.description"
                    />
                </div>
            </div>
        </section>

        <!-- Players -->
        <section class="rounded-lg border border-darker-700 bg-darker-900 p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-widest text-primary-400">
                    Players
                </h3>
                <span class="text-xs text-darker-500">
                    {{ form.player_ids.length }} selected
                </span>
            </div>

            <FormInput
                v-model:model_value="player_search"
                placeholder="Search players by name or email"
            />

            <p v-if="form.errors.player_ids" class="mt-2 text-xs text-red-400">
                {{ form.errors.player_ids }}
            </p>

            <div v-if="player_options.length === 0" class="mt-4 rounded-md border border-dashed border-darker-700 bg-darker-800 px-4 py-6 text-center text-sm text-darker-500">
                There are no players to add yet.
            </div>

            <div v-else class="mt-4 max-h-72 space-y-1 overflow-y-auto rounded-md border border-darker-700 bg-darker-800 p-2">
                <div
                    v-for="option in visible_player_options"
                    :key="option.value"
                    class="flex items-center gap-3 rounded px-3 py-2 text-sm transition hover:bg-darker-700"
                    :class="isPlayerSelected(option.value) ? 'text-darker-100' : 'text-darker-400'"
                >
                    <input
                        :id="`player-${option.value}`"
                        v-model="form.player_ids"
                        type="checkbox"
                        class="h-4 w-4 shrink-0 cursor-pointer rounded border-darker-600 bg-darker-900 text-primary-500 focus:ring-primary-500/40"
                        :value="option.value"
                    />
                    <label :for="`player-${option.value}`" class="flex-1 cursor-pointer">
                        {{ option.label }}
                    </label>
                </div>

                <p v-if="visible_player_options.length === 0" class="px-3 py-2 text-sm text-darker-500">
                    No players match "{{ player_search }}".
                </p>
            </div>
        </section>

        <!-- Schedule -->
        <section class="rounded-lg border border-darker-700 bg-darker-900 p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-widest text-primary-400">
                    Schedule
                </h3>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-md border border-darker-700 bg-darker-800 px-3 py-1.5 text-xs font-medium text-darker-300 transition hover:border-primary-700 hover:text-primary-300"
                    @click="addSession"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Session
                </button>
            </div>

            <div v-if="session_count === 0" class="rounded-md border border-dashed border-darker-700 bg-darker-800 px-4 py-8 text-center">
                <p class="text-sm text-darker-500">No sessions scheduled yet.</p>
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="(session, index) in form.sessions"
                    :key="index"
                    class="rounded-md border border-darker-700 bg-darker-800 p-4"
                >
                    <div class="grid gap-3 sm:grid-cols-12">
                        <div class="sm:col-span-4">
                            <InputLabel class="mb-1" :for="`session-${index}-date`" value="Date & Time" />
                            <FormInput
                                :id="`session-${index}-date`"
                                v-model:model_value="session.scheduled_at"
                                type="datetime-local"
                                :error="form.errors[`sessions.${index}.scheduled_at`]"
                                required
                            />
                        </div>

                        <div class="sm:col-span-5">
                            <InputLabel class="mb-1" :for="`session-${index}-title`" value="Title" />
                            <FormInput
                                :id="`session-${index}-title`"
                                v-model:model_value="session.title"
                                placeholder="Optional session title"
                                :error="form.errors[`sessions.${index}.title`]"
                            />
                        </div>

                        <div class="sm:col-span-3">
                            <InputLabel class="mb-1" :for="`session-${index}-status`" value="Status" />
                            <FormSelect
                                :id="`session-${index}-status`"
                                v-model:model_value="session.status"
                                :options="session_status_options"
                                :error="form.errors[`sessions.${index}.status`]"
                            />
                        </div>
                    </div>

                    <div class="mt-3">
                        <InputLabel class="mb-1" :for="`session-${index}-notes`" value="Notes" />
                        <FormTextarea
                            :id="`session-${index}-notes`"
                            v-model:model_value="session.notes"
                            placeholder="Anything the Keeper needs to remember"
                            :rows="2"
                            :error="form.errors[`sessions.${index}.notes`]"
                        />
                    </div>

                    <div class="mt-3 flex justify-end">
                        <button
                            type="button"
                            class="text-xs font-medium text-red-400 transition hover:text-red-300"
                            @click="removeSession(index)"
                        >
                            Remove session
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <div class="flex items-center justify-end gap-3">
            <slot name="actions" />
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-primary-500 px-5 py-2.5 text-xs font-semibold uppercase tracking-widest text-darker-950 transition hover:bg-primary-400 disabled:opacity-50"
                :disabled="form.processing"
            >
                {{ submit_label }}
            </button>
        </div>
    </form>
</template>
