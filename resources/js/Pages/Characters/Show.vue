<script setup>
import App from '@/Layouts/App.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

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
         *   skills: { skill_id: number, name: string, value: number, base: number }[],
         *   possessions: Record<string, { name: string, value: number, modifier_sign: string|null, modifier: number }[]>
         * }}
         */
        type: Object,
        required: true,
    },
    occupation_options: {
        type: Array,
        required: true,
    },
    all_skills: {
        type: Array,
        required: true,
    },
    all_possessions: {
        type: Object,
        required: true,
    },
    occupation_skills: {
        type: Object,
        required: true,
    },
})

const StatAbbr = (name) => ({
    Strength: 'STR', Constitution: 'CON', Size: 'SIZ',
    Dexterity: 'DEX', Appearance: 'APP', Intelligence: 'INT',
    Power: 'POW', Education: 'EDU', Luck: 'LCK',
})[name] ?? name

const PossessionTypeOrder = ['Weapon', 'Arcane', 'Investigative', 'Essential', 'Key']

const SortedPossessionTypes = Object.keys(props.character.possessions).sort(
    (a, b) => PossessionTypeOrder.indexOf(a) - PossessionTypeOrder.indexOf(b)
)

const BaseWidth = (base) => `${Math.min(base, 100)}%`
const EarnedWidth = (value, base) => `${Math.max(0, Math.min(value, 100) - base)}%`
const EarnedLeft = (base) => `${Math.min(base, 100)}%`

// ─── Basic info form ──────────────────────────────────────────────────────────
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

const StartEditing = () => { is_editing.value = true }
const CancelEditing = () => { form.reset(); is_editing.value = false }
const SaveCharacter = () => {
    form.patch(route('characters.update', props.character.id), {
        onSuccess: () => { is_editing.value = false },
    })
}

// ─── Stats form ───────────────────────────────────────────────────────────────
const is_editing_stats = ref(false)

const stats_form = useForm({
    stats: props.character.stats.map(s => ({ name: s.name, value: s.value })),
})

const StartEditingStats = () => { is_editing_stats.value = true }
const CancelEditingStats = () => { stats_form.reset(); is_editing_stats.value = false }
const SaveStats = () => {
    stats_form.patch(route('characters.stats.update', props.character.id), {
        onSuccess: () => { is_editing_stats.value = false },
    })
}

// ─── Skills form ──────────────────────────────────────────────────────────────
const is_editing_skills = ref(false)

const skills_form = useForm({
    skills: props.all_skills.map(s => {
        const existing = props.character.skills.find(cs => cs.skill_id === s.id)
        return { skill_id: s.id, value: existing?.value ?? s.base }
    }),
})

const occupationSkillNames = computed(() =>
    props.occupation_skills[props.character.occupation] ?? []
)

const edu = computed(() => stats_form.stats.find(s => s.name === 'Education')?.value
    ?? props.character.stats.find(s => s.name === 'Education')?.value ?? 0)
const int_ = computed(() => stats_form.stats.find(s => s.name === 'Intelligence')?.value
    ?? props.character.stats.find(s => s.name === 'Intelligence')?.value ?? 0)

const occupationPool = computed(() => edu.value * 4)
const interestPool = computed(() => int_.value * 2)

const baseSkillValues = computed(() => {
    const dex_val = props.character.stats.find(s => s.name === 'Dexterity')?.value ?? 0
    const edu_val = edu.value
    const map = {}
    props.all_skills.forEach(s => { map[s.id] = s.base })
    const dodge = props.all_skills.find(s => s.name === 'Dodge')
    if (dodge) { map[dodge.id] = Math.floor(dex_val / 2) }
    const langOwn = props.all_skills.find(s => s.name === 'Language (Own)')
    if (langOwn) { map[langOwn.id] = edu_val }
    return map
})

const occupationPointsSpent = computed(() => {
    let total = 0
    skills_form.skills.forEach(s => {
        const skillMeta = props.all_skills.find(a => a.id === s.skill_id)
        if (!skillMeta) { return }
        if (occupationSkillNames.value.includes(skillMeta.name)) {
            total += Math.max(0, s.value - (baseSkillValues.value[s.skill_id] ?? skillMeta.base))
        }
    })
    return total
})

const interestPointsSpent = computed(() => {
    let total = 0
    skills_form.skills.forEach(s => {
        const skillMeta = props.all_skills.find(a => a.id === s.skill_id)
        if (!skillMeta) { return }
        if (!occupationSkillNames.value.includes(skillMeta.name)) {
            total += Math.max(0, s.value - (baseSkillValues.value[s.skill_id] ?? skillMeta.base))
        }
    })
    return total
})

const occupationRemaining = computed(() => occupationPool.value - occupationPointsSpent.value)
const interestRemaining = computed(() => interestPool.value - interestPointsSpent.value)

const isSkillLocked = (skillName) => ['Dodge', 'Language (Own)', 'Cthulhu Mythos'].includes(skillName)

const getSkillFormIndex = (skillId) => skills_form.skills.findIndex(s => s.skill_id === skillId)

const sortedAllSkills = computed(() => {
    const occNames = occupationSkillNames.value
    return [...props.all_skills].sort((a, b) => {
        const aOcc = occNames.includes(a.name)
        const bOcc = occNames.includes(b.name)
        if (aOcc && !bOcc) { return -1 }
        if (!aOcc && bOcc) { return 1 }
        return a.name.localeCompare(b.name)
    })
})

const StartEditingSkills = () => { is_editing_skills.value = true }
const CancelEditingSkills = () => { skills_form.reset(); is_editing_skills.value = false }
const SaveSkills = () => {
    skills_form.patch(route('characters.skills.update', props.character.id), {
        onSuccess: () => { is_editing_skills.value = false },
    })
}

// ─── Possessions form ─────────────────────────────────────────────────────────
const is_editing_possessions = ref(false)

const buildInitialPossessions = () => {
    const list = []
    Object.values(props.character.possessions).flat().forEach(p => {
        const allP = Object.values(props.all_possessions).flat().find(ap => ap.name === p.name)
        if (allP) {
            list.push({
                possession_id: allP.id,
                modifier_sign: p.modifier_sign ?? null,
                modifier: p.modifier ?? null,
            })
        }
    })
    return list
}

const possessions_form = useForm({
    possessions: buildInitialPossessions(),
    new_possessions: [],
})

const isPossessionChecked = (possessionId) =>
    possessions_form.possessions.some(p => p.possession_id === possessionId)

const togglePossession = (possession) => {
    const idx = possessions_form.possessions.findIndex(p => p.possession_id === possession.id)
    if (idx === -1) {
        possessions_form.possessions.push({ possession_id: possession.id, modifier_sign: null, modifier: null })
    } else {
        possessions_form.possessions.splice(idx, 1)
    }
}

const getPossessionPivot = (possessionId) =>
    possessions_form.possessions.find(p => p.possession_id === possessionId)

// New possession dialog
const show_dialog = ref(false)
const dialog_type = ref('Weapon')
const new_item = ref({ type: 'Weapon', name: '', value: 0, modifier_sign: null, modifier: null })

const openDialog = (type) => {
    dialog_type.value = type
    new_item.value = { type, name: '', value: 0, modifier_sign: null, modifier: null }
    show_dialog.value = true
}

const saveNewItem = () => {
    if (!new_item.value.name.trim()) { return }
    possessions_form.new_possessions.push({ ...new_item.value })
    show_dialog.value = false
}

const StartEditingPossessions = () => { is_editing_possessions.value = true }
const CancelEditingPossessions = () => { possessions_form.reset(); is_editing_possessions.value = false }
const SavePossessions = () => {
    possessions_form.patch(route('characters.possessions.update', props.character.id), {
        onSuccess: () => { is_editing_possessions.value = false },
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
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xs font-semibold uppercase tracking-widest text-darker-500">
                        Characteristics
                    </h2>
                    <button
                        v-if="!is_editing_stats"
                        class="rounded-lg border border-darker-600 px-3 py-1.5 text-xs font-semibold text-darker-300 transition hover:border-primary-400 hover:text-primary-300"
                        @click="StartEditingStats"
                    >
                        Edit
                    </button>
                    <div v-else class="flex gap-2">
                        <button
                            class="rounded-lg border border-darker-600 px-3 py-1.5 text-xs font-semibold text-darker-300 transition hover:border-darker-400 hover:text-white"
                            :disabled="stats_form.processing"
                            @click="CancelEditingStats"
                        >
                            Cancel
                        </button>
                        <button
                            class="rounded-lg bg-primary-500 px-3 py-1.5 text-xs font-semibold text-darker-900 transition hover:bg-primary-400 disabled:opacity-50"
                            :disabled="stats_form.processing"
                            @click="SaveStats"
                        >
                            {{ stats_form.processing ? 'Saving…' : 'Save' }}
                        </button>
                    </div>
                </div>

                <!-- View -->
                <div v-if="!is_editing_stats" class="grid grid-cols-3 gap-3 sm:grid-cols-5 lg:grid-cols-9">
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

                <!-- Edit -->
                <div v-else class="grid grid-cols-3 gap-3 sm:grid-cols-5 lg:grid-cols-9">
                    <div
                        v-for="(stat, i) in stats_form.stats"
                        :key="stat.name"
                        class="flex flex-col items-center rounded-xl border border-primary-300 bg-white px-2 py-4 shadow-xs"
                    >
                        <span class="text-xs font-bold uppercase tracking-widest text-secondary-600">
                            {{ StatAbbr(stat.name) }}
                        </span>
                        <input
                            v-model.number="stats_form.stats[i].value"
                            type="number"
                            min="1"
                            max="100"
                            class="mt-2 w-full text-center text-xl font-bold text-darker-900 outline-none border-b border-primary-300 bg-transparent"
                        />
                        <div class="mt-3 w-full border-t border-darker-100 pt-2">
                            <div class="flex justify-between text-xs text-darker-400">
                                <span>½ {{ Math.floor(stat.value / 2) }}</span>
                                <span>⅕ {{ Math.floor(stat.value / 5) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Skills -->
            <section>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-xs font-semibold uppercase tracking-widest text-darker-500">
                        Skills
                    </h2>
                    <template v-if="!is_editing_skills">
                        <button
                            class="rounded-lg border border-darker-600 px-3 py-1.5 text-xs font-semibold text-darker-300 transition hover:border-primary-400 hover:text-primary-300"
                            @click="StartEditingSkills"
                        >
                            Edit
                        </button>
                    </template>
                    <template v-else>
                        <div class="flex flex-wrap items-center gap-3">
                            <Badge :variant="occupationRemaining < 0 ? 'destructive' : 'secondary'" class="text-xs">
                                Occ: {{ occupationRemaining }} / {{ occupationPool }}
                            </Badge>
                            <Badge :variant="interestRemaining < 0 ? 'destructive' : 'outline'" class="text-xs">
                                Int: {{ interestRemaining }} / {{ interestPool }}
                            </Badge>
                            <button
                                class="rounded-lg border border-darker-600 px-3 py-1.5 text-xs font-semibold text-darker-300 transition hover:border-darker-400 hover:text-white"
                                :disabled="skills_form.processing"
                                @click="CancelEditingSkills"
                            >
                                Cancel
                            </button>
                            <button
                                class="rounded-lg bg-primary-500 px-3 py-1.5 text-xs font-semibold text-darker-900 transition hover:bg-primary-400 disabled:opacity-50"
                                :disabled="skills_form.processing"
                                @click="SaveSkills"
                            >
                                {{ skills_form.processing ? 'Saving…' : 'Save' }}
                            </button>
                        </div>
                    </template>
                </div>

                <!-- View -->
                <div v-if="!is_editing_skills" class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
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

                <!-- Edit -->
                <div v-else class="space-y-1.5">
                    <div
                        v-for="skill in sortedAllSkills"
                        :key="skill.id"
                        :class="[
                            'flex items-center gap-3 rounded-lg border px-3 py-2',
                            occupationSkillNames.includes(skill.name)
                                ? 'border-primary-300 bg-primary-50'
                                : 'border-darker-200 bg-white',
                        ]"
                    >
                        <div class="min-w-0 flex-1">
                            <span class="text-sm font-medium text-darker-800">{{ skill.name }}</span>
                            <span v-if="occupationSkillNames.includes(skill.name)" class="ml-2 text-xs text-primary-500">occ</span>
                        </div>
                        <span class="text-xs text-darker-400 w-8 text-right">{{ baseSkillValues[skill.id] ?? skill.base }}</span>
                        <input
                            :value="skills_form.skills[getSkillFormIndex(skill.id)]?.value ?? 0"
                            @input="e => skills_form.skills[getSkillFormIndex(skill.id)].value = Math.min(99, Math.max(0, Number(e.target.value)))"
                            type="number"
                            min="0"
                            max="99"
                            :disabled="isSkillLocked(skill.name)"
                            class="w-16 rounded border border-darker-300 px-2 py-1 text-center text-sm font-medium text-darker-900 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                    </div>
                </div>
            </section>

            <!-- Possessions -->
            <section>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xs font-semibold uppercase tracking-widest text-darker-500">
                        Possessions
                    </h2>
                    <template v-if="!is_editing_possessions">
                        <button
                            class="rounded-lg border border-darker-600 px-3 py-1.5 text-xs font-semibold text-darker-300 transition hover:border-primary-400 hover:text-primary-300"
                            @click="StartEditingPossessions"
                        >
                            Edit
                        </button>
                    </template>
                    <template v-else>
                        <div class="flex gap-2">
                            <button
                                class="rounded-lg border border-darker-600 px-3 py-1.5 text-xs font-semibold text-darker-300 transition hover:border-darker-400 hover:text-white"
                                :disabled="possessions_form.processing"
                                @click="CancelEditingPossessions"
                            >
                                Cancel
                            </button>
                            <button
                                class="rounded-lg bg-primary-500 px-3 py-1.5 text-xs font-semibold text-darker-900 transition hover:bg-primary-400 disabled:opacity-50"
                                :disabled="possessions_form.processing"
                                @click="SavePossessions"
                            >
                                {{ possessions_form.processing ? 'Saving…' : 'Save' }}
                            </button>
                        </div>
                    </template>
                </div>

                <!-- View -->
                <div v-if="!is_editing_possessions">
                    <div v-if="Object.keys(character.possessions).length" class="space-y-6">
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
                    <p v-else class="text-sm text-darker-400">No possessions yet.</p>
                </div>

                <!-- Edit -->
                <div v-else>
                    <Tabs :default-value="PossessionTypeOrder[0]">
                        <TabsList class="mb-4 flex flex-wrap gap-1 h-auto">
                            <TabsTrigger
                                v-for="type in PossessionTypeOrder"
                                :key="type"
                                :value="type"
                            >
                                {{ type }}
                            </TabsTrigger>
                        </TabsList>

                        <TabsContent
                            v-for="type in PossessionTypeOrder"
                            :key="type"
                            :value="type"
                        >
                            <div class="space-y-2">
                                <div
                                    v-for="item in (all_possessions[type] ?? [])"
                                    :key="item.id"
                                    class="flex flex-wrap items-center gap-3 rounded-lg border border-darker-200 bg-white px-4 py-3 shadow-xs"
                                >
                                    <Checkbox
                                        :id="`edit-poss-${item.id}`"
                                        :checked="isPossessionChecked(item.id)"
                                        @update:checked="() => togglePossession(item)"
                                    />
                                    <label :for="`edit-poss-${item.id}`" class="flex-1 cursor-pointer text-sm font-medium text-darker-800">
                                        {{ item.name }}
                                    </label>
                                    <span class="text-sm text-darker-400">{{ item.value }}</span>

                                    <template v-if="isPossessionChecked(item.id)">
                                        <Select
                                            :model-value="getPossessionPivot(item.id)?.modifier_sign ?? ''"
                                            @update:model-value="v => getPossessionPivot(item.id).modifier_sign = v || null"
                                        >
                                            <SelectTrigger class="w-24">
                                                <SelectValue placeholder="±" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Plus">+</SelectItem>
                                                <SelectItem value="Minus">−</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <input
                                            :value="getPossessionPivot(item.id)?.modifier ?? ''"
                                            @input="e => getPossessionPivot(item.id).modifier = e.target.value ? Number(e.target.value) : null"
                                            type="number"
                                            placeholder="Mod"
                                            class="w-20 rounded border border-darker-300 px-2 py-1 text-center text-sm text-darker-900"
                                        />
                                    </template>
                                </div>

                                <!-- New items added via dialog -->
                                <div
                                    v-for="(item, idx) in possessions_form.new_possessions.filter(p => p.type === type)"
                                    :key="`new-${idx}`"
                                    class="flex items-center gap-3 rounded-lg border border-primary-300 bg-primary-50 px-4 py-3"
                                >
                                    <span class="text-sm font-medium text-primary-700">{{ item.name }}</span>
                                    <span class="text-xs text-darker-400">{{ item.value }}</span>
                                    <button
                                        class="ml-auto text-xs text-darker-400 hover:text-secondary-500"
                                        type="button"
                                        @click="possessions_form.new_possessions.splice(possessions_form.new_possessions.indexOf(item), 1)"
                                    >
                                        ✕
                                    </button>
                                </div>

                                <Button variant="outline" size="sm" class="mt-2" @click="openDialog(type)">
                                    + Add custom
                                </Button>
                            </div>
                        </TabsContent>
                    </Tabs>
                </div>
            </section>

        </div>
    </App>

    <!-- New Possession Dialog -->
    <Dialog v-model:open="show_dialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Add Custom Item</DialogTitle>
            </DialogHeader>
            <div class="space-y-4 py-2">
                <div>
                    <Label>Name *</Label>
                    <Input v-model="new_item.name" placeholder="Item name" class="mt-1" />
                </div>
                <div>
                    <Label>Type</Label>
                    <Select v-model="new_item.type">
                        <SelectTrigger class="mt-1 w-full">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="t in PossessionTypeOrder" :key="t" :value="t">{{ t }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label>Base Value</Label>
                    <Input v-model.number="new_item.value" type="number" min="0" class="mt-1" />
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <Label>Modifier Sign</Label>
                        <Select v-model="new_item.modifier_sign">
                            <SelectTrigger class="mt-1 w-full">
                                <SelectValue placeholder="None" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="Plus">+</SelectItem>
                                <SelectItem value="Minus">−</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="flex-1">
                        <Label>Modifier</Label>
                        <Input v-model.number="new_item.modifier" type="number" placeholder="0" class="mt-1" />
                    </div>
                </div>
            </div>
            <DialogFooter>
                <Button variant="outline" @click="show_dialog = false">Cancel</Button>
                <Button @click="saveNewItem" :disabled="!new_item.name.trim()">Add Item</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
