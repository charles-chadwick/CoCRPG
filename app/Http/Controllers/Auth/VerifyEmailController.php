<?php

namespace App\Http\Controllers\Auth;

use App\Actions\VerifyUserEmailAction;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request, VerifyUserEmailAction $action): RedirectResponse
    {
        $action->handle($request->user());

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
