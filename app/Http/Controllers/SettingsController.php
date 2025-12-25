<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     * Replaces Jetstream's profile page UI.
     */
    public function index(Request $request)
    {
        // Default tab
        $tab = $request->query('tab', 'profile');

        // Allowed tabs (safety)
        $allowedTabs = [
            'profile',
            'hosting',
            'password',
            'security',
            'sessions',
            'danger',
        ];

        if (! in_array($tab, $allowedTabs)) {
            $tab = 'profile';
        }

        return view('pages.settings.index', [
            'tab' => $tab,
        ]);
    }

    /**
     * Update basic profile info (name, email).
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update($data);

        return back()
            ->with('status', 'Profile updated successfully.');
    }

    /**
     * Update password (Jetstream-compatible logic).
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()
            ->with('status', 'Password updated successfully.');
    }

    /**
     * Update hosting preferences (stub for now).
     * Later: store in user_settings table or JSON column.
     */
    public function updateHosting(Request $request)
    {
        // Example validation (expand later)
        $request->validate([
            'default_region' => ['nullable', 'string'],
            'status_alerts' => ['nullable', 'boolean'],
        ]);

        // TODO: persist to DB
        // $request->user()->settings()->updateOrCreate(...)

        return back()
            ->with('status', 'Hosting settings saved.');
    }
    
    public function destroyAccount(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        // Logout first (important for session safety)
        Auth::logout();

        // Delete the user
        // NOTE: If you have related records with FK constraints,
        // you may need to delete/detach them first (servers, tickets, etc.)
        $user->delete();

        // Invalidate the session and regenerate token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('status', 'Your account has been deleted.');
    }
}
