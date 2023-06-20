<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_type');
            $table->string('file_id')->unique();
            $table->longText('file_caption');
            $table->float('file_size', 8);
            $table->string('file_uploader');
            $table->integer('file_views');
            $table->integer('file_downloads');
            $table->string('file_parent_path');
            $table->longText('file_description');
            $table->string('file_thumbnails');
            $table->string('file_download_link');
            $table->longText('file_remote_id', 100);
            $table->string('file_path', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
