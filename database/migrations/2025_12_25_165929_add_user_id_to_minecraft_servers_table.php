<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minecraft_servers', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('id'); // adjust if you want after another column

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('minecraft_servers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
