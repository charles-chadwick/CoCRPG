<?php

namespace App\Http\Controllers\Auth;

use App\Actions\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request, RegisterUserAction $action): RedirectResponse
    {
        $action->handle($request->validated());

        return redirect(route('dashboard', absolute: false));
    }
}
