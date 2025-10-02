<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeaturePreferenceMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_preference_metas', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type', 80);
            $table->integer('owner_id');
            $table->unique(['key', 'owner_type', 'owner_id']);
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feature_preference_metas');
    }
}
