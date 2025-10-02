<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('onboarding_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('type', ['flow', 'checklist', 'launcher']);
            $table->string('tour_id')->comment('The ID from usertour.io');
            $table->string('url_matcher')->comment('URL path or route name to trigger on, * for all');
            $table->enum('scope', ['business', 'user'])->default('user')->comment('For progress tracking');
            $table->integer('points')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('onboarding_steps');
    }
};