<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;

class DeleteUserAccountAction
{
    public function __construct(private LogOutUserAction $logOutUser) {}

    /**
     * Delete the given user's account and end their session.
     */
    public function handle(Request $request, User $user): void
    {
        $this->logOutUser->handle($request);

        $user->delete();
    }
}
