<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    campaigns: Array,
});

const page = usePage();

const can_create_campaign = computed(
    () => page.props.auth.user.is_game_master || page.props.auth.user.is_admin,
);

function formatSessionDate(iso_date) {
    if (!iso_date) {
        return null;
    }

    return new Date(iso_date).toLocaleString(undefined, {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Campaigns" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-darker-100">Campaigns</h2>
                <Link
                    v-if="can_create_campaign"
                    :href="route('campaigns.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-primary-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-darker-950 transition hover:bg-primary-400"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Campaign
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Empty State -->
                <div v-if="campaigns.length === 0" class="flex flex-col items-center justify-center rounded-lg border border-dashed border-darker-700 bg-darker-900 py-20 text-center">
                    <div class="mb-4 rounded-full bg-darker-800 p-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-darker-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-darker-200">No campaigns yet</h3>
                    <p class="mb-6 text-sm text-darker-500">
                        {{ can_create_campaign
                            ? 'Create a campaign and gather your investigators.'
                            : 'A Keeper will add you to a campaign when one begins.' }}
                    </p>
                    <Link
                        v-if="can_create_campaign"
                        :href="route('campaigns.create')"
                        class="inline-flex items-center gap-2 rounded-md bg-primary-500 px-5 py-2.5 text-sm font-semibold text-darker-950 transition hover:bg-primary-400"
                    >
                        Create Your First Campaign
                    </Link>
                </div>

                <!-- Campaign Grid -->
                <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="campaign in campaigns"
                        :key="campaign.id"
                        :href="route('campaigns.show', campaign.id)"
                        class="group relative flex flex-col rounded-lg border border-darker-700 bg-darker-900 p-6 transition hover:border-primary-700 hover:bg-darker-800"
                    >
                        <div class="mb-4 flex items-start justify-between gap-3">
                            <span
                                class="inline-block rounded px-2 py-0.5 text-xs font-medium"
                                :class="campaign.is_game_master
                                    ? 'bg-primary-900 text-primary-300'
                                    : 'bg-secondary-900 text-secondary-300'"
                            >
                                {{ campaign.is_game_master ? 'Keeper' : 'Player' }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-darker-600 transition group-hover:text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </div>

                        <h3 class="mb-1 text-lg font-semibold text-darker-100 transition-colors group-hover:text-primary-400">
                            {{ campaign.title }}
                        </h3>

                        <p class="mb-4 text-xs text-darker-500">Kept by {{ campaign.game_master }}</p>

                        <p v-if="campaign.description" class="mb-4 line-clamp-3 text-sm text-darker-400">
                            {{ campaign.description }}
                        </p>

                        <div class="mt-auto space-y-1 border-t border-darker-800 pt-4 text-xs text-darker-500">
                            <div class="flex gap-4">
                                <span>{{ campaign.player_count }} {{ campaign.player_count === 1 ? 'player' : 'players' }}</span>
                                <span>{{ campaign.session_count }} {{ campaign.session_count === 1 ? 'session' : 'sessions' }}</span>
                            </div>
                            <p v-if="campaign.next_session_at" class="text-primary-400">
                                Next: {{ formatSessionDate(campaign.next_session_at) }}
                            </p>
                            <p v-else class="text-darker-600">Nothing scheduled</p>
                        </div>

                        <div class="absolute inset-x-0 bottom-0 h-0.5 rounded-b-lg bg-primary-500 opacity-0 transition group-hover:opacity-100"></div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
