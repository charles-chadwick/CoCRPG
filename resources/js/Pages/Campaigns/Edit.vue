<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CampaignForm from '@/Pages/Campaigns/Partials/CampaignForm.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    campaign: Object,
    player_options: Array,
    session_status_options: Array,
});

const form = useForm({
    title: props.campaign.title,
    description: props.campaign.description ?? '',
    player_ids: [...props.campaign.player_ids],
    sessions: props.campaign.sessions.map((session) => ({
        id: session.id,
        title: session.title ?? '',
        scheduled_at: session.scheduled_at,
        status: session.status,
        notes: session.notes ?? '',
    })),
});

const confirming_deletion = ref(false);

function submit() {
    form.patch(route('campaigns.update', props.campaign.id));
}

function deleteCampaign() {
    router.delete(route('campaigns.destroy', props.campaign.id));
}
</script>

<template>
    <Head :title="`Edit ${campaign.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-darker-100">Edit Campaign</h2>
                <Link
                    :href="route('campaigns.show', campaign.id)"
                    class="text-xs font-medium uppercase tracking-widest text-darker-400 transition hover:text-darker-200"
                >
                    Back to Campaign
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <CampaignForm
                    :form="form"
                    :player_options="player_options"
                    :session_status_options="session_status_options"
                    submit_label="Save Changes"
                    @submit="submit"
                >
                    <template #actions>
                        <button
                            type="button"
                            class="text-xs font-medium uppercase tracking-widest text-red-400 transition hover:text-red-300"
                            @click="confirming_deletion = true"
                        >
                            Delete Campaign
                        </button>
                    </template>
                </CampaignForm>
            </div>
        </div>

        <Modal :show="confirming_deletion" @close="confirming_deletion = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-darker-100">
                    Delete "{{ campaign.title }}"?
                </h3>
                <p class="mt-2 text-sm text-darker-400">
                    The campaign and its schedule will be removed. Characters played in it keep
                    their sheets but lose their campaign.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="confirming_deletion = false">Cancel</SecondaryButton>
                    <DangerButton @click="deleteCampaign">Delete Campaign</DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
