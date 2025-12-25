<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MinecraftServerController;
use App\Http\Controllers\Admin\MinecraftConsoleController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\User\ServerDashboardController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

// Pricing (public)
Route::get('/pricing/minecraft', [PricingController::class, 'minecraft'])
    ->name('pricing.minecraft');

// Public profile pages (viewable by anyone)
Route::get('/u/{user:username}', [ProfileController::class, 'show'])
    ->name('users.show');

// Home
Route::get('/', function () {
    return view('pages.index');
})->name('home');


/*
|--------------------------------------------------------------------------
| Settings (Your custom settings replaces Jetstream)
|--------------------------------------------------------------------------
| Jetstream uses route('profile.show') and path /user/profile.
| We override that route to point to YOUR SettingsController.
*/
Route::middleware(['auth'])->group(function () {

    // Your custom settings page (direct)
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index');

    // Override Jetstream profile route so route('profile.show') goes to your page
    Route::get('/user/profile', [SettingsController::class, 'index'])
        ->name('profile.show');

    // ✅ Add these update routes
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/hosting', [SettingsController::class, 'updateHosting'])->name('settings.hosting.update');


    Route::delete('/settings/account', [SettingsController::class, 'destroyAccount'])
    ->name('settings.account.destroy');
});


/*
|--------------------------------------------------------------------------
| Checkout (auth-only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ✅ MUST be first
    Route::get('/checkout/success', [CheckoutController::class, 'success'])
        ->name('checkout.success');

    Route::get('/checkout/{slug}', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout/{slug}', [CheckoutController::class, 'provision'])
        ->name('checkout.provision');
});


/*
|--------------------------------------------------------------------------
| Dashboard Redirect (Jetstream/Verified)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');
});


/*
|--------------------------------------------------------------------------
| User Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::prefix('servers')->name('servers.')->group(function () {
        Route::get('/', [ServerDashboardController::class, 'index'])->name('index');
        Route::get('/{server}', [ServerDashboardController::class, 'show'])->name('show');

        Route::post('/{server}/start', [ServerDashboardController::class, 'start'])->name('start');
        Route::post('/{server}/stop', [ServerDashboardController::class, 'stop'])->name('stop');
        Route::post('/{server}/restart', [ServerDashboardController::class, 'restart'])->name('restart');

        Route::get('/{server}/console/logs', [ServerDashboardController::class, 'consoleLogs'])->name('console.logs');
        Route::post('/{server}/console/send', [ServerDashboardController::class, 'consoleSend'])->name('console.send');
    });
});


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
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

        Route::get('/minecraft', [MinecraftServerController::class, 'index'])
            ->name('minecraft.index');

        Route::get('/minecraft/create', [MinecraftServerController::class, 'create'])
            ->name('minecraft.create');

        Route::post('/minecraft', [MinecraftServerController::class, 'store'])
            ->name('minecraft.store');

        Route::get('/minecraft/{server}', [MinecraftServerController::class, 'show'])
            ->name('minecraft.show');

        Route::post('/minecraft/{server}/start', [MinecraftServerController::class, 'start'])
            ->name('minecraft.start');

        Route::post('/minecraft/{server}/stop', [MinecraftServerController::class, 'stop'])
            ->name('minecraft.stop');

        Route::post('/minecraft/{server}/restart', [MinecraftServerController::class, 'restart'])
            ->name('minecraft.restart');

        Route::delete('/minecraft/{server}', [MinecraftServerController::class, 'destroy'])
            ->name('minecraft.destroy');

        // Console
        Route::get('/minecraft/{server}/console/logs', [MinecraftConsoleController::class, 'logs'])
            ->name('minecraft.console.logs');

        Route::post('/minecraft/{server}/console/send', [MinecraftConsoleController::class, 'send'])
            ->name('minecraft.console.send');
    });
