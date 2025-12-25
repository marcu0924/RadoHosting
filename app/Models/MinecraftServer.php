<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MinecraftServer extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'ram',
        'cpu',
        'port',
        'running',
        'environment',
        'container_name',
        'world_path',
    ];

    protected $casts = [
        'environment' => 'array',
        'running'     => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
