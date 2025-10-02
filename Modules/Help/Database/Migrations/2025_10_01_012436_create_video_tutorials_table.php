<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_tutorials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('youtube_url');
            $table->string('video_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('display_url');
            $table->string('hashtags')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_tutorials');
    }
};