<script setup>
import App from '@/Layouts/App.vue'

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
         *   skills: { name: string, value: number }[],
         *   possessions: Record<string, { name: string, value: number, modifier_sign: string|null, modifier: number }[]>
         * }}
         */
        type: Object,
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

const StatBarWidth = (value) => `${Math.min(value, 100)}%`
</script>

<template>
    <App>
        <div class="space-y-8 py-8">

            <!-- Header -->
            <div class="rounded-2xl bg-darker-800 px-8 py-6 text-white">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-primary-300">
                            {{ character.name }}
                        </h1>
                        <p class="mt-1 text-lg text-secondary-300">{{ character.occupation }}</p>
                    </div>
                    <div class="gap-x-6 gap-y-2 text-sm text-white">
                        <p><span class="text-darker-500 font-bold">Age:</span> {{ character.age }}</p>
                        <p><span class="text-darker-500 font-bold">Gender:</span> {{ character.gender }}</p>
                        <p><span class="text-darker-500 font-bold">Birthplace:</span> {{ character.birthplace }}</p>
                        <p><span class="text-darker-500 font-bold">Residence:</span> {{ character.residence }}</p>
                    </div>
                </div>
                <p v-if="character.description" class="mt-4 text-sm leading-relaxed text-darker-400">
                    {{ character.description }}
                </p>
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
                                    {{ skill.value }}%
                                </span>
                            </div>
                            <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-darker-100">
                                <div
                                    class="h-full rounded-full bg-primary-400 transition-all"
                                    :style="{ width: StatBarWidth(skill.value) }"
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
                                        {{ item.modifier_sign === 'Plus' ? '+' : '-' }}{{ item.modifier }}
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
