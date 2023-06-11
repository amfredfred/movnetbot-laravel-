<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bot_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('first_name', 100)->nullable();
            $table->string('chat_id', 100)->nullable();
            $table->string('last_name', 100)->nullable()->default('text');
            $table->string('last_checkin')->nullable()->default(null);
            $table->json('search_history')->nullable();
            $table->json('watch_history')->nullable();
            $table->json('saved_items')->nullable();
            $table->integer('query_count')->unsigned()->nullable()->default(0);
            $table->string('user_type', 100)->nullable()->default('text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_users');
    }
};
