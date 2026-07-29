<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    characters: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-darker-100">
                    My Characters
                </h2>
                <Link
                    :href="route('characters.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-primary-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-darker-950 transition hover:bg-primary-400"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Character
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Empty State -->
                <div v-if="characters.length === 0" class="flex flex-col items-center justify-center rounded-lg border border-dashed border-darker-700 bg-darker-900 py-20 text-center">
                    <div class="mb-4 rounded-full bg-darker-800 p-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-darker-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-darker-200">No characters yet</h3>
                    <p class="mb-6 text-sm text-darker-500">Create your first investigator to begin your descent into madness.</p>
                    <Link
                        :href="route('characters.create')"
                        class="inline-flex items-center gap-2 rounded-md bg-primary-500 px-5 py-2.5 text-sm font-semibold text-darker-950 transition hover:bg-primary-400"
                    >
                        Create Your First Character
                    </Link>
                </div>

                <!-- Character Grid -->
                <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="character in characters"
                        :key="character.id"
                        :href="route('characters.show', character.id)"
                        class="group relative rounded-lg border border-darker-700 bg-darker-900 p-6 transition hover:border-primary-700 hover:bg-darker-800"
                    >
                        <!-- Occupation badge -->
                        <div class="mb-4 flex items-start justify-between">
                            <span class="inline-block rounded bg-secondary-900 px-2 py-0.5 text-xs font-medium text-secondary-300">
                                {{ character.occupation }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-darker-600 transition group-hover:text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </div>

                        <!-- Name -->
                        <h3 class="mb-1 text-lg font-semibold text-darker-100 group-hover:text-primary-400 transition-colors">
                            {{ character.name }}
                        </h3>

                        <!-- Campaign -->
                        <p class="mb-2 text-xs text-darker-400">
                            {{ character.campaign ? character.campaign.title : 'No campaign' }}
                        </p>

                        <!-- Meta -->
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-darker-500">
                            <span v-if="character.age">Age {{ character.age }}</span>
                            <span v-if="character.gender">{{ character.gender }}</span>
                            <span v-if="character.birthplace">{{ character.birthplace }}</span>
                        </div>

                        <!-- Hover accent line -->
                        <div class="absolute inset-x-0 bottom-0 h-0.5 rounded-b-lg bg-primary-500 opacity-0 transition group-hover:opacity-100"></div>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
