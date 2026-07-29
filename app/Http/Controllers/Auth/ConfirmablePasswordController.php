<?php

namespace App\Http\Controllers\Auth;

use App\Actions\ConfirmUserPasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ConfirmPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): Response
    {
        return Inertia::render('Auth/ConfirmPassword');
    }

    /**
     * Confirm the user's password.
     *
     * @throws ValidationException
     */
    public function store(ConfirmPasswordRequest $request, ConfirmUserPasswordAction $action): RedirectResponse
    {
        if (! $action->handle($request->user(), $request->validated()['password'])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
