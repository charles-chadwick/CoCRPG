<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    campaign: Object,
    can_update: Boolean,
});

const upcoming_sessions = computed(() =>
    props.campaign.sessions.filter((session) => session.status === 'Scheduled'),
);

const past_sessions = computed(() =>
    props.campaign.sessions.filter((session) => session.status !== 'Scheduled'),
);

const status_classes = {
    Scheduled: 'bg-primary-900 text-primary-300',
    Played: 'bg-secondary-900 text-secondary-300',
    Cancelled: 'bg-red-900/60 text-red-300',
};

function formatSessionDate(local_date_time) {
    return new Date(local_date_time).toLocaleString(undefined, {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head :title="campaign.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-darker-100">{{ campaign.title }}</h2>
                    <p class="text-xs text-darker-500">Kept by {{ campaign.game_master.name }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('campaigns.index')"
                        class="text-xs font-medium uppercase tracking-widest text-darker-400 transition hover:text-darker-200"
                    >
                        All Campaigns
                    </Link>
                    <Link
                        v-if="can_update"
                        :href="route('campaigns.edit', campaign.id)"
                        class="inline-flex items-center rounded-md bg-primary-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-darker-950 transition hover:bg-primary-400"
                    >
                        Edit
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
                <!-- Main column -->
                <div class="space-y-6 lg:col-span-2">
                    <section v-if="campaign.description" class="rounded-lg border border-darker-700 bg-darker-900 p-6">
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-widest text-primary-400">
                            About
                        </h3>
                        <p class="whitespace-pre-line text-sm leading-relaxed text-darker-300">
                            {{ campaign.description }}
                        </p>
                    </section>

                    <section class="rounded-lg border border-darker-700 bg-darker-900 p-6">
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-primary-400">
                            Upcoming Sessions
                        </h3>

                        <p v-if="upcoming_sessions.length === 0" class="text-sm text-darker-500">
                            Nothing scheduled.
                        </p>

                        <ul v-else class="space-y-3">
                            <li
                                v-for="session in upcoming_sessions"
                                :key="session.id"
                                class="rounded-md border border-darker-700 bg-darker-800 p-4"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-darker-100">
                                            {{ session.title || 'Untitled session' }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-darker-400">
                                            {{ formatSessionDate(session.scheduled_at) }}
                                        </p>
                                    </div>
                                    <span class="shrink-0 rounded px-2 py-0.5 text-xs font-medium" :class="status_classes[session.status]">
                                        {{ session.status }}
                                    </span>
                                </div>
                                <p v-if="session.notes" class="mt-3 whitespace-pre-line border-t border-darker-700 pt-3 text-xs text-darker-400">
                                    {{ session.notes }}
                                </p>
                            </li>
                        </ul>
                    </section>

                    <section v-if="past_sessions.length > 0" class="rounded-lg border border-darker-700 bg-darker-900 p-6">
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-darker-400">
                            Past Sessions
                        </h3>
                        <ul class="space-y-2">
                            <li
                                v-for="session in past_sessions"
                                :key="session.id"
                                class="flex items-center justify-between gap-3 rounded-md border border-darker-800 bg-darker-800/50 px-4 py-3"
                            >
                                <div>
                                    <p class="text-sm text-darker-300">
                                        {{ session.title || 'Untitled session' }}
                                    </p>
                                    <p class="text-xs text-darker-500">
                                        {{ formatSessionDate(session.scheduled_at) }}
                                    </p>
                                </div>
                                <span class="shrink-0 rounded px-2 py-0.5 text-xs font-medium" :class="status_classes[session.status]">
                                    {{ session.status }}
                                </span>
                            </li>
                        </ul>
                    </section>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <section class="rounded-lg border border-darker-700 bg-darker-900 p-6">
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-primary-400">
                            Players
                        </h3>

                        <p v-if="campaign.players.length === 0" class="text-sm text-darker-500">
                            No players yet.
                        </p>

                        <ul v-else class="space-y-2">
                            <li
                                v-for="player in campaign.players"
                                :key="player.id"
                                class="rounded-md bg-darker-800 px-3 py-2"
                            >
                                <p class="text-sm text-darker-200">{{ player.name }}</p>
                                <p class="text-xs text-darker-500">{{ player.email }}</p>
                            </li>
                        </ul>
                    </section>

                    <section class="rounded-lg border border-darker-700 bg-darker-900 p-6">
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-primary-400">
                            Investigators
                        </h3>

                        <p v-if="campaign.characters.length === 0" class="text-sm text-darker-500">
                            No characters have joined this campaign.
                        </p>

                        <ul v-else class="space-y-2">
                            <li v-for="character in campaign.characters" :key="character.id">
                                <Link
                                    :href="route('characters.show', character.id)"
                                    class="group block rounded-md bg-darker-800 px-3 py-2 transition hover:bg-darker-700"
                                >
                                    <p class="text-sm text-darker-200 transition group-hover:text-primary-400">
                                        {{ character.name }}
                                    </p>
                                    <p class="text-xs text-darker-500">
                                        {{ character.occupation }} &middot; {{ character.player }}
                                    </p>
                                </Link>
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
