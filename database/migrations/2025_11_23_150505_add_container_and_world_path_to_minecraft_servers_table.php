<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minecraft_servers', function (Blueprint $table) {
            $table->string('container_name')->nullable()->after('port');
            $table->string('world_path')->nullable()->after('container_name');
        });
    }

    public function down(): void
    {
        Schema::table('minecraft_servers', function (Blueprint $table) {
            $table->dropColumn(['container_name', 'world_path']);
        });
    }
};
