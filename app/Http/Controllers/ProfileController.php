<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        // Optional: load relationships later (servers, subscriptions, etc.)
        // $user->loadCount('servers');

        return view('pages.profile.show', [
            'user' => $user,
        ]);
    }
}
