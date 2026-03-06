<?php

use App\Http\Controllers\CharacterController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
});

Route::get('/characters/create', [CharacterController::class, 'create'])->name('characters.create');
Route::post('/characters', [CharacterController::class, 'store'])->name('characters.store');
Route::get('/characters/{character}', [CharacterController::class, 'show'])->name('characters.show');
Route::patch('/characters/{character}', [CharacterController::class, 'update'])->name('characters.update');
Route::patch('/characters/{character}/stats', [CharacterController::class, 'updateStats'])->name('characters.stats.update');
Route::patch('/characters/{character}/skills', [CharacterController::class, 'updateSkills'])->name('characters.skills.update');
Route::patch('/characters/{character}/possessions', [CharacterController::class, 'updatePossessions'])->name('characters.possessions.update');
