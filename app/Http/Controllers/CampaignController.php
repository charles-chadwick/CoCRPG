<?php

namespace App\Http\Controllers;

use App\Actions\CreateCampaignAction;
use App\Actions\DeleteCampaignAction;
use App\Actions\GetCampaignDetailsAction;
use App\Actions\GetCampaignFormOptionsAction;
use App\Actions\GetUserCampaignsAction;
use App\Actions\UpdateCampaignAction;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function index(GetUserCampaignsAction $action): Response
    {
        $this->authorize('viewAny', Campaign::class);

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $action->handle(auth()->user()),
        ]);
    }

    public function create(GetCampaignFormOptionsAction $formOptions): Response
    {
        $this->authorize('create', Campaign::class);

        return Inertia::render('Campaigns/Create', $formOptions->handle());
    }

    public function store(StoreCampaignRequest $request, CreateCampaignAction $action): RedirectResponse
    {
        $this->authorize('create', Campaign::class);

        $campaign = $action->handle(auth()->user(), $request->validated());

        return redirect()->route('campaigns.show', $campaign);
    }

    public function show(Campaign $campaign, GetCampaignDetailsAction $details): Response
    {
        $this->authorize('view', $campaign);

        return Inertia::render('Campaigns/Show', [
            'campaign' => $details->handle($campaign),
            'can_update' => auth()->user()->can('update', $campaign),
        ]);
    }

    public function edit(Campaign $campaign, GetCampaignDetailsAction $details, GetCampaignFormOptionsAction $formOptions): Response
    {
        $this->authorize('update', $campaign);

        return Inertia::render('Campaigns/Edit', [
            ...$formOptions->handle(),
            'campaign' => $details->handle($campaign),
        ]);
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign, UpdateCampaignAction $action): RedirectResponse
    {
        $this->authorize('update', $campaign);

        $action->handle($campaign, $request->validated());

        return redirect()->route('campaigns.show', $campaign);
    }

    public function destroy(Campaign $campaign, DeleteCampaignAction $action): RedirectResponse
    {
        $this->authorize('delete', $campaign);

        $action->handle($campaign);

        return redirect()->route('campaigns.index');
    }
}
