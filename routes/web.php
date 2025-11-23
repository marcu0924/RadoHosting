<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MinecraftServerController;

Route::get('/', function () {
    return view('pages.index');
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Admin home
        Route::get('/', [DashboardController::class, 'index'])
            ->name('index');

        /*
        |--------------------------------------------------------------------------
        | Minecraft Server Routes
        |--------------------------------------------------------------------------
        */

        // Index (list)
        Route::get('/minecraft', [MinecraftServerController::class, 'index'])
            ->name('minecraft.index');

        // Create form
        Route::get('/minecraft/create', [MinecraftServerController::class, 'create'])
            ->name('minecraft.create');

        // Store new server
        Route::post('/minecraft', [MinecraftServerController::class, 'store'])
            ->name('minecraft.store');

        // Show server
        Route::get('/minecraft/{server}', [MinecraftServerController::class, 'show'])
            ->name('minecraft.show');

        // Start server
        Route::post('/minecraft/{server}/start', [MinecraftServerController::class, 'start'])
            ->name('minecraft.start');

        // Stop server
        Route::post('/minecraft/{server}/stop', [MinecraftServerController::class, 'stop'])
            ->name('minecraft.stop');

        // Restart server
        Route::post('/minecraft/{server}/restart', [MinecraftServerController::class, 'restart'])
            ->name('minecraft.restart');

        // Delete server
        Route::delete('/minecraft/{server}', [MinecraftServerController::class, 'destroy'])
            ->name('minecraft.destroy');

    });
