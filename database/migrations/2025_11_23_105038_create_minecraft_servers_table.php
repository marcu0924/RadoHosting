<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minecraft_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('ram');
            $table->unsignedInteger('cpu');
            $table->unsignedInteger('port');
            $table->boolean('running')->default(false);
            $table->json('environment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minecraft_servers');
    }
};
