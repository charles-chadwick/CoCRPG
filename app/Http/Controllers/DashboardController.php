<?php

namespace App\Http\Controllers;

use App\Actions\GetUserCharactersAction;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(GetUserCharactersAction $action): Response
    {
        return Inertia::render('Dashboard', [
            'characters' => $action->handle(auth()->user()),
        ]);
    }
}
