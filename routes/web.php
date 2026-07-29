<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('campaigns', CampaignController::class);

    Route::get('/characters/create', [CharacterController::class, 'create'])->name('characters.create');
    Route::post('/characters', [CharacterController::class, 'store'])->name('characters.store');
    Route::get('/characters/{character}', [CharacterController::class, 'show'])->name('characters.show');
    Route::patch('/characters/{character}', [CharacterController::class, 'update'])->name('characters.update');
    Route::patch('/characters/{character}/stats', [CharacterController::class, 'updateStats'])->name('characters.stats.update');
    Route::patch('/characters/{character}/skills', [CharacterController::class, 'updateSkills'])->name('characters.skills.update');
    Route::patch('/characters/{character}/possessions', [CharacterController::class, 'updatePossessions'])->name('characters.possessions.update');
});

require __DIR__.'/auth.php';
