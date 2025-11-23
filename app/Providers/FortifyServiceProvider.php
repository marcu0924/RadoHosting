<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Default Jetstream/Fortify wiring
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // ✅ Custom authenticateUsing so we explicitly control "remember me"
        Fortify::authenticateUsing(function (LoginRequest $request) {
            // This is the single field on your form: "Email or Username"
            $login    = $request->input('login');      // 👈 from your <input name="login">
            $password = $request->input('password');
            $remember = $request->boolean('remember'); // 👈 same remember logic you already had

            // Find by email OR username
            $user = User::where('email', $login)
                ->orWhere('username', $login)
                ->first();

            // Manual password check but still honoring "remember"
            if ($user && Hash::check($password, $user->password)) {
                Auth::login($user, $remember);
                $request->session()->regenerate();

                // Fortify expects a User model or null
                return $user;
            }

            // Will trigger the usual "These credentials do not match our records." response
            return null;
        });

        // Login rate limiting
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        // Two-factor rate limiting
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
