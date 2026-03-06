<script setup>
import App from '@/Layouts/App.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    character: {
        /** @type {{
         *   id: number,
         *   name: string,
         *   occupation: string,
         *   age: number,
         *   gender: string,
         *   birthplace: string,
         *   residence: string,
         *   description: string,
         *   stats: { name: string, value: number, half: number, fifth: number }[],
         *   skills: { name: string, value: number, base: number }[],
         *   possessions: Record<string, { name: string, value: number, modifier_sign: string|null, modifier: number }[]>
         * }}
         */
        type: Object,
        required: true,
    },
    occupation_options: {
        /** @type {{ value: string, label: string }[]} */
        type: Array,
        required: true,
    },
})

const StatAbbr = (name) => ({
    Strength: 'STR',
    Constitution: 'CON',
    Size: 'SIZ',
    Dexterity: 'DEX',
    Appearance: 'APP',
    Intelligence: 'INT',
    Power: 'POW',
    Education: 'EDU',
    Luck: 'LCK',
})[name] ?? name

const PossessionTypeOrder = ['Weapon', 'Arcane', 'Investigative', 'Essential', 'Key']

const SortedPossessionTypes = Object.keys(props.character.possessions).sort(
    (a, b) => PossessionTypeOrder.indexOf(a) - PossessionTypeOrder.indexOf(b)
)

const BaseWidth = (base) => `${Math.min(base, 100)}%`
const EarnedWidth = (value, base) => `${Math.max(0, Math.min(value, 100) - base)}%`
const EarnedLeft = (base) => `${Math.min(base, 100)}%`

const is_editing = ref(false)

const form = useForm({
    name: props.character.name,
    occupation: props.character.occupation,
    age: props.character.age,
    gender: props.character.gender,
    birthplace: props.character.birthplace,
    residence: props.character.residence,
    description: props.character.description,
})

const StartEditing = () => {
    is_editing.value = true
}

const CancelEditing = () => {
    form.reset()
    is_editing.value = false
}

const SaveCharacter = () => {
    form.patch(route('characters.update', props.character.id), {
        onSuccess: () => { is_editing.value = false },
    })
}
</script>

<template>
    <App>
        <div class="space-y-8 py-8">

            <!-- Header -->
            <div class="rounded-2xl bg-darker-800 px-8 py-6 text-white">

                <!-- View mode -->
                <template v-if="!is_editing">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-primary-300">
                                {{ character.name }}
                            </h1>
                            <p class="mt-1 text-lg text-secondary-300">{{ character.occupation }}</p>
                        </div>
                        <div class="flex items-start gap-6">
                            <div class="gap-x-6 gap-y-2 text-sm text-white">
                                <p><span class="font-bold text-darker-500">Age:</span> {{ character.age }}</p>
                                <p><span class="font-bold text-darker-500">Gender:</span> {{ character.gender }}</p>
                                <p><span class="font-bold text-darker-500">Birthplace:</span> {{ character.birthplace }}</p>
                                <p><span class="font-bold text-darker-500">Residence:</span> {{ character.residence }}</p>
                            </div>
                            <button
                                class="shrink-0 rounded-lg border border-darker-600 px-3 py-1.5 text-xs font-semibold text-darker-300 transition hover:border-primary-400 hover:text-primary-300"
                                @click="StartEditing"
                            >
                                Edit
                            </button>
                        </div>
                    </div>
                    <p v-if="character.description" class="mt-4 text-sm leading-relaxed text-darker-400">
                        {{ character.description }}
                    </p>
                </template>

                <!-- Edit mode -->
                <template v-else>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-white">
                        <div class="sm:col-span-2">
                            <FormInput
                                name="name"
                                placeholder="Character name"
                                :model_value="form.name"
                                :error="form.errors.name"

                                @update:model_value="form.name = $event"
                            />
                        </div>
                        <FormSelect
                            name="occupation"
                            :options="occupation_options"
                            :model_value="form.occupation"
                            :error="form.errors.occupation"
                            @update:model_value="form.occupation = $event"
                        />
                        <FormInput
                            name="age"
                            type="number"
                            placeholder="Age"
                            :model_value="form.age"
                            :error="form.errors.age"
                            @update:model_value="form.age = $event"
                        />
                        <FormInput
                            name="gender"
                            placeholder="Gender"
                            :model_value="form.gender"
                            :error="form.errors.gender"
                            @update:model_value="form.gender = $event"
                        />
                        <FormInput
                            name="birthplace"
                            placeholder="Birthplace"
                            :model_value="form.birthplace"
                            :error="form.errors.birthplace"
                            @update:model_value="form.birthplace = $event"
                        />
                        <FormInput
                            name="residence"
                            placeholder="Residence"
                            :model_value="form.residence"
                            :error="form.errors.residence"
                            @update:model_value="form.residence = $event"
                        />
                        <div class="sm:col-span-2">
                            <FormTextarea
                                name="description"
                                placeholder="Description"
                                :rows="3"
                                :model_value="form.description"
                                :error="form.errors.description"
                                @update:model_value="form.description = $event"
                            />
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button
                            class="rounded-lg border border-darker-600 px-4 py-2 text-sm font-semibold text-darker-300 transition hover:border-darker-400 hover:text-white"
                            :disabled="form.processing"
                            @click="CancelEditing"
                        >
                            Cancel
                        </button>
                        <button
                            class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-semibold text-darker-900 transition hover:bg-primary-400 disabled:opacity-50"
                            :disabled="form.processing"
                            @click="SaveCharacter"
                        >
                            {{ form.processing ? 'Saving…' : 'Save' }}
                        </button>
                    </div>
                </template>

            </div>

            <!-- Stats -->
            <section>
                <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-darker-500">
                    Characteristics
                </h2>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-5 lg:grid-cols-9">
                    <div
                        v-for="stat in character.stats"
                        :key="stat.name"
                        class="flex flex-col items-center rounded-xl border border-darker-200 bg-white px-2 py-4 shadow-xs"
                    >
                        <span class="text-xs font-bold uppercase tracking-widest text-secondary-600">
                            {{ StatAbbr(stat.name) }}
                        </span>
                        <span class="mt-2 text-3xl font-bold text-darker-900">{{ stat.value }}</span>
                        <div class="mt-3 w-full border-t border-darker-100 pt-2">
                            <div class="flex justify-between text-xs text-darker-400">
                                <span>½ {{ stat.half }}</span>
                                <span>⅕ {{ stat.fifth }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Skills -->
            <section>
                <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-darker-500">
                    Skills
                </h2>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="skill in character.skills"
                        :key="skill.name"
                        class="flex items-center gap-3 rounded-lg border border-darker-200 bg-white px-4 py-3 shadow-xs"
                    >
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate text-sm font-medium text-darker-800">
                                    {{ skill.name }}
                                </span>
                                <span class="shrink-0 text-sm font-bold text-primary-600">
                                    {{ skill.value }}
                                </span>
                            </div>
                            <div class="relative mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-darker-100">
                                <div
                                    class="absolute left-0 top-0 h-full bg-primary-200"
                                    :style="{ width: BaseWidth(skill.base) }"
                                />
                                <div
                                    class="absolute top-0 h-full bg-primary-500"
                                    :style="{ left: EarnedLeft(skill.base), width: EarnedWidth(skill.value, skill.base) }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Possessions -->
            <section v-if="Object.keys(character.possessions).length">
                <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-darker-500">
                    Possessions
                </h2>
                <div class="space-y-6">
                    <div v-for="type in SortedPossessionTypes" :key="type">
                        <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-secondary-500">
                            {{ type }}
                        </h3>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="item in character.possessions[type]"
                                :key="item.name"
                                class="flex items-center justify-between rounded-lg border border-darker-200 bg-white px-4 py-3 shadow-xs"
                            >
                                <span class="text-sm font-medium text-darker-800">{{ item.name }}</span>
                                <div class="flex items-center gap-1 text-sm">
                                    <span class="text-darker-400">{{ item.value }}</span>
                                    <span
                                        v-if="item.modifier"
                                        :class="item.modifier_sign === 'Plus'
                                            ? 'text-primary-600'
                                            : 'text-secondary-600'"
                                        class="font-semibold"
                                    >
                                        {{ item.modifier_sign === 'Plus' ? '+' : '-' }}{{ Math.abs(item.modifier) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </App>
</template>
