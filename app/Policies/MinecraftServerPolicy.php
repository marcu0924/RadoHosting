<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MinecraftServer;

class MinecraftServerPolicy
{
    public function view(User $user, MinecraftServer $server): bool
    {
        return $user->role === 'admin' || $server->user_id === $user->id;
    }

    public function control(User $user, MinecraftServer $server): bool
    {
        return $user->role === 'admin' || $server->user_id === $user->id;
    }
}
