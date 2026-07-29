<?php

namespace App\Http\Controllers\Auth;

use App\Actions\UpdateUserPasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(UpdatePasswordRequest $request, UpdateUserPasswordAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated()['password']);

        return back();
    }
}
