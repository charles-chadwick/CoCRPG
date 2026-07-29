<?php

namespace App\Http\Controllers;

use App\Actions\DeleteUserAccountAction;
use App\Actions\UpdateUserProfileAction;
use App\Http\Requests\ProfileDeleteRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, UpdateUserProfileAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(ProfileDeleteRequest $request, DeleteUserAccountAction $action): RedirectResponse
    {
        $action->handle($request, $request->user());

        return Redirect::to('/');
    }
}
