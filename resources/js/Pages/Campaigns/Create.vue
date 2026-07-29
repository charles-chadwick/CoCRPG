<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CampaignForm from '@/Pages/Campaigns/Partials/CampaignForm.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    player_options: Array,
    session_status_options: Array,
});

const form = useForm({
    title: '',
    description: '',
    player_ids: [],
    sessions: [],
});

function submit() {
    form.post(route('campaigns.store'));
}
</script>

<template>
    <Head title="New Campaign" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-darker-100">New Campaign</h2>
                <Link
                    :href="route('campaigns.index')"
                    class="text-xs font-medium uppercase tracking-widest text-darker-400 transition hover:text-darker-200"
                >
                    Back to Campaigns
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <CampaignForm
                    :form="form"
                    :player_options="player_options"
                    :session_status_options="session_status_options"
                    submit_label="Create Campaign"
                    @submit="submit"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
