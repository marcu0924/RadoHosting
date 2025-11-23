<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinecraftServer extends Model
{
    protected $fillable = [
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
}
