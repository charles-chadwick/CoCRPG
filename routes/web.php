<?php

use App\Http\Controllers\CharacterController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
});

Route::get('/characters/{character}', [CharacterController::class, 'show'])->name('characters.show');
