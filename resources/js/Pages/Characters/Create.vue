<script setup>
import App from '@/Layouts/App.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
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
    occupation_options: { type: Array, required: true },
    all_skills: { type: Array, required: true },
    all_possessions: { type: Object, required: true },
    stat_names: { type: Array, required: true },
    occupation_skills: { type: Object, required: true },
})

// ─── Step ────────────────────────────────────────────────────────────────────
const step = ref(1)
const steps = ['Basics', 'Characteristics', 'Skills', 'Items']

// ─── Form ────────────────────────────────────────────────────────────────────
const form = useForm({
    name: '',
    occupation: '',
    age: '',
    gender: '',
    birthplace: '',
    residence: '',
    description: '',
    stats: props.stat_names.map(name => ({ name, value: 0 })),
    skills: props.all_skills.map(s => ({ skill_id: s.id, value: s.base })),
    possessions: [],
    new_possessions: [],
})

// ─── Stat helpers ─────────────────────────────────────────────────────────────
const StatAbbr = (name) => ({
    Strength: 'STR', Constitution: 'CON', Size: 'SIZ',
    Dexterity: 'DEX', Appearance: 'APP', Intelligence: 'INT',
    Power: 'POW', Education: 'EDU', Luck: 'LCK',
})[name] ?? name

const rollDice = (count, sides) => Array.from({ length: count }, () => Math.floor(Math.random() * sides) + 1)

const rollStatValue = (name) => {
    if (['Size', 'Intelligence', 'Education'].includes(name)) {
        return (rollDice(2, 6).reduce((a, b) => a + b, 0) + 6) * 5
    }
    return rollDice(3, 6).reduce((a, b) => a + b, 0) * 5
}

const rollStat = (index) => {
    form.stats[index].value = rollStatValue(form.stats[index].name)
}

const rollAll = () => {
    form.stats.forEach((_, i) => rollStat(i))
}

const statHalf = (value) => Math.floor(value / 2)
const statFifth = (value) => Math.floor(value / 5)

// ─── Derived stats ────────────────────────────────────────────────────────────
const edu = computed(() => form.stats.find(s => s.name === 'Education')?.value ?? 0)
const int_ = computed(() => form.stats.find(s => s.name === 'Intelligence')?.value ?? 0)
const dex = computed(() => form.stats.find(s => s.name === 'Dexterity')?.value ?? 0)

// ─── Skills ────────────────────────────────────────────────────────────────────
const occupationPool = computed(() => edu.value * 4)
const interestPool = computed(() => int_.value * 2)

const occupationSkillNames = computed(() =>
    form.occupation ? (props.occupation_skills[form.occupation] ?? []) : []
)

const baseValues = computed(() => {
    const map = {}
    props.all_skills.forEach(s => { map[s.id] = s.base })
    // Dodge = DEX / 2
    const dodge = props.all_skills.find(s => s.name === 'Dodge')
    if (dodge) { map[dodge.id] = Math.floor(dex.value / 2) }
    // Language (Own) = EDU
    const langOwn = props.all_skills.find(s => s.name === 'Language (Own)')
    if (langOwn) { map[langOwn.id] = edu.value }
    return map
})

// When stats change, update Dodge and Language (Own) values
watch([dex, edu], () => {
    const dodge = props.all_skills.find(s => s.name === 'Dodge')
    const langOwn = props.all_skills.find(s => s.name === 'Language (Own)')
    if (dodge) {
        const idx = form.skills.findIndex(s => s.skill_id === dodge.id)
        if (idx !== -1) { form.skills[idx].value = Math.floor(dex.value / 2) }
    }
    if (langOwn) {
        const idx = form.skills.findIndex(s => s.skill_id === langOwn.id)
        if (idx !== -1) { form.skills[idx].value = edu.value }
    }
})

const occupationPointsSpent = computed(() => {
    let total = 0
    form.skills.forEach(s => {
        const skillMeta = props.all_skills.find(a => a.id === s.skill_id)
        if (!skillMeta) { return }
        if (occupationSkillNames.value.includes(skillMeta.name)) {
            total += Math.max(0, s.value - (baseValues.value[s.skill_id] ?? skillMeta.base))
        }
    })
    return total
})

const interestPointsSpent = computed(() => {
    let total = 0
    form.skills.forEach(s => {
        const skillMeta = props.all_skills.find(a => a.id === s.skill_id)
        if (!skillMeta) { return }
        if (!occupationSkillNames.value.includes(skillMeta.name)) {
            total += Math.max(0, s.value - (baseValues.value[s.skill_id] ?? skillMeta.base))
        }
    })
    return total
})

const occupationRemaining = computed(() => occupationPool.value - occupationPointsSpent.value)
const interestRemaining = computed(() => interestPool.value - interestPointsSpent.value)

const isSkillLocked = (skillName) => ['Dodge', 'Language (Own)', 'Cthulhu Mythos'].includes(skillName)

const getSkillIndex = (skillId) => form.skills.findIndex(s => s.skill_id === skillId)

const sortedSkills = computed(() => {
    const occNames = occupationSkillNames.value
    return [...props.all_skills].sort((a, b) => {
        const aOcc = occNames.includes(a.name)
        const bOcc = occNames.includes(b.name)
        if (aOcc && !bOcc) { return -1 }
        if (!aOcc && bOcc) { return 1 }
        return a.name.localeCompare(b.name)
    })
})

// ─── Possessions ──────────────────────────────────────────────────────────────
const PossessionTypeOrder = ['Weapon', 'Essential', 'Arcane', 'Investigative', 'Key']

const isPossessionChecked = (possessionId) =>
    form.possessions.some(p => p.possession_id === possessionId)

const togglePossession = (possession) => {
    const idx = form.possessions.findIndex(p => p.possession_id === possession.id)
    if (idx === -1) {
        form.possessions.push({ possession_id: possession.id, modifier_sign: null, modifier: null })
    } else {
        form.possessions.splice(idx, 1)
    }
}

const getPossessionPivot = (possessionId) =>
    form.possessions.find(p => p.possession_id === possessionId)

// ─── New possession dialog ────────────────────────────────────────────────────
const show_dialog = ref(false)
const dialog_tab = ref('Weapon')
const new_item = ref({ type: 'Weapon', name: '', value: 0, modifier_sign: null, modifier: null })

const openDialog = (type) => {
    dialog_tab.value = type
    new_item.value = { type, name: '', value: 0, modifier_sign: null, modifier: null }
    show_dialog.value = true
}

const saveNewItem = () => {
    if (!new_item.value.name.trim()) { return }
    form.new_possessions.push({ ...new_item.value })
    show_dialog.value = false
}

// ─── Navigation ──────────────────────────────────────────────────────────────
const canNext = computed(() => {
    if (step.value === 1) {
        return form.name.trim() && form.occupation && form.age
    }
    if (step.value === 2) {
        return form.stats.every(s => s.value > 0)
    }
    return true
})

const goNext = () => { if (step.value < 4) { step.value++ } }
const goBack = () => { if (step.value > 1) { step.value-- } }

const submit = () => {
    form.post(route('characters.store'))
}
</script>

<template>
    <App>
        <div class="py-8 space-y-6">

            <!-- Step indicator -->
            <div class="flex items-center gap-0">
                <template v-for="(label, i) in steps" :key="i">
                    <div class="flex items-center gap-2">
                        <div :class="[
                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold transition',
                            step > i + 1
                                ? 'bg-primary-500 text-darker-900'
                                : step === i + 1
                                    ? 'bg-primary-500 text-darker-900 ring-4 ring-primary-200'
                                    : 'bg-darker-200 text-darker-500',
                        ]">
                            <span v-if="step > i + 1">✓</span>
                            <span v-else>{{ i + 1 }}</span>
                        </div>
                        <span :class="[
                            'text-sm font-medium hidden sm:block',
                            step === i + 1 ? 'text-primary-600' : 'text-darker-400',
                        ]">{{ label }}</span>
                    </div>
                    <div
                        v-if="i < steps.length - 1"
                        :class="['mx-3 h-0.5 flex-1', step > i + 1 ? 'bg-primary-400' : 'bg-darker-200']"
                    />
                </template>
            </div>

            <!-- Step content -->
            <div class="rounded-2xl bg-darker-800 px-6 py-8 text-white sm:px-8">

                <!-- Step 1: Basics -->
                <template v-if="step === 1">
                    <h2 class="mb-6 text-xl font-bold text-primary-300">Basic Information</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <Label class="text-darker-300">Name *</Label>
                            <Input
                                v-model="form.name"
                                placeholder="Character name"
                                class="mt-1"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-secondary-400">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <Label class="text-darker-300">Occupation *</Label>
                            <Select v-model="form.occupation">
                                <SelectTrigger class="mt-1 w-full">
                                    <SelectValue placeholder="Select occupation" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in occupation_options"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.occupation" class="mt-1 text-xs text-secondary-400">{{ form.errors.occupation }}</p>
                        </div>

                        <div>
                            <Label class="text-darker-300">Age *</Label>
                            <Input
                                v-model.number="form.age"
                                type="number"
                                placeholder="Age"
                                min="1"
                                max="999"
                                class="mt-1"
                            />
                            <p v-if="form.errors.age" class="mt-1 text-xs text-secondary-400">{{ form.errors.age }}</p>
                        </div>

                        <div>
                            <Label class="text-darker-300">Gender</Label>
                            <Input v-model="form.gender" placeholder="Gender" class="mt-1" />
                        </div>

                        <div>
                            <Label class="text-darker-300">Birthplace</Label>
                            <Input v-model="form.birthplace" placeholder="Birthplace" class="mt-1" />
                        </div>

                        <div>
                            <Label class="text-darker-300">Residence</Label>
                            <Input v-model="form.residence" placeholder="Residence" class="mt-1" />
                        </div>

                        <div class="sm:col-span-2">
                            <Label class="text-darker-300">Description</Label>
                            <Textarea
                                v-model="form.description"
                                placeholder="Character description…"
                                :rows="3"
                                class="mt-1"
                            />
                        </div>
                    </div>
                </template>

                <!-- Step 2: Characteristics -->
                <template v-else-if="step === 2">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-primary-300">Characteristics</h2>
                        <Button variant="outline" size="sm" @click="rollAll">
                            🎲 Roll All
                        </Button>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div
                            v-for="(stat, i) in form.stats"
                            :key="stat.name"
                            class="rounded-xl border border-darker-600 bg-darker-700 p-4"
                        >
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-widest text-secondary-400">
                                    {{ StatAbbr(stat.name) }}
                                </span>
                                <button
                                    class="text-xs text-darker-400 hover:text-primary-300 transition"
                                    @click="rollStat(i)"
                                    type="button"
                                >
                                    🎲
                                </button>
                            </div>
                            <Input
                                v-model.number="stat.value"
                                type="number"
                                min="1"
                                max="100"
                                class="mb-2 text-center text-lg font-bold"
                            />
                            <div class="flex justify-between text-xs text-darker-500">
                                <span>½ {{ statHalf(stat.value) }}</span>
                                <span>⅕ {{ statFifth(stat.value) }}</span>
                            </div>
                        </div>
                    </div>
                    <p v-if="form.errors.stats" class="mt-3 text-xs text-secondary-400">{{ form.errors.stats }}</p>
                </template>

                <!-- Step 3: Skills -->
                <template v-else-if="step === 3">
                    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
                        <h2 class="text-xl font-bold text-primary-300">Skills</h2>
                        <div class="flex gap-4 text-sm">
                            <div>
                                <span class="text-darker-400">Occupation pool: </span>
                                <Badge :variant="occupationRemaining < 0 ? 'destructive' : 'secondary'">
                                    {{ occupationRemaining }} / {{ occupationPool }} remaining
                                </Badge>
                            </div>
                            <div>
                                <span class="text-darker-400">Interest pool: </span>
                                <Badge :variant="interestRemaining < 0 ? 'destructive' : 'outline'">
                                    {{ interestRemaining }} / {{ interestPool }} remaining
                                </Badge>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <div
                            v-for="skill in sortedSkills"
                            :key="skill.id"
                            :class="[
                                'flex items-center gap-3 rounded-lg border px-3 py-2',
                                occupationSkillNames.includes(skill.name)
                                    ? 'border-primary-500/30 bg-primary-900/20'
                                    : 'border-darker-600 bg-darker-700/50',
                            ]"
                        >
                            <div class="min-w-0 flex-1">
                                <span class="text-sm font-medium text-white">{{ skill.name }}</span>
                                <span v-if="occupationSkillNames.includes(skill.name)" class="ml-2 text-xs text-primary-400">occ</span>
                            </div>
                            <span class="text-xs text-darker-500 w-8 text-right">{{ baseValues[skill.id] ?? skill.base }}</span>
                            <Input
                                :model-value="form.skills[getSkillIndex(skill.id)]?.value ?? 0"
                                @update:model-value="v => form.skills[getSkillIndex(skill.id)].value = Math.min(99, Math.max(0, Number(v)))"
                                type="number"
                                min="0"
                                max="99"
                                :disabled="isSkillLocked(skill.name)"
                                class="w-16 text-center disabled:opacity-50"
                            />
                        </div>
                    </div>
                </template>

                <!-- Step 4: Items -->
                <template v-else-if="step === 4">
                    <h2 class="mb-6 text-xl font-bold text-primary-300">Items & Possessions</h2>
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
                                    class="flex flex-wrap items-center gap-3 rounded-lg border border-darker-600 bg-darker-700 px-4 py-3"
                                >
                                    <Checkbox
                                        :id="`poss-${item.id}`"
                                        :checked="isPossessionChecked(item.id)"
                                        @update:checked="() => togglePossession(item)"
                                    />
                                    <label :for="`poss-${item.id}`" class="flex-1 cursor-pointer text-sm font-medium text-white">
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
                                        <Input
                                            :model-value="getPossessionPivot(item.id)?.modifier ?? ''"
                                            @update:model-value="v => getPossessionPivot(item.id).modifier = v ? Number(v) : null"
                                            type="number"
                                            placeholder="Mod"
                                            class="w-20"
                                        />
                                    </template>
                                </div>

                                <!-- New items added via dialog -->
                                <div
                                    v-for="(item, idx) in form.new_possessions.filter(p => p.type === type)"
                                    :key="`new-${idx}`"
                                    class="flex items-center gap-3 rounded-lg border border-primary-600/40 bg-primary-900/10 px-4 py-3"
                                >
                                    <span class="text-sm font-medium text-primary-300">{{ item.name }}</span>
                                    <span class="text-xs text-darker-400">{{ item.value }}</span>
                                    <button
                                        class="ml-auto text-xs text-darker-400 hover:text-secondary-400"
                                        type="button"
                                        @click="form.new_possessions.splice(form.new_possessions.indexOf(item), 1)"
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
                </template>

            </div>

            <!-- Navigation -->
            <div class="flex justify-between">
                <Button
                    v-if="step > 1"
                    variant="outline"
                    @click="goBack"
                    :disabled="form.processing"
                >
                    ← Back
                </Button>
                <div v-else />

                <div class="flex gap-3">
                    <Button
                        v-if="step < 4"
                        :disabled="!canNext"
                        @click="goNext"
                    >
                        Next →
                    </Button>
                    <Button
                        v-else
                        :disabled="form.processing"
                        @click="submit"
                    >
                        {{ form.processing ? 'Saving…' : 'Create Character' }}
                    </Button>
                </div>
            </div>

        </div>

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
    </App>
</template>
