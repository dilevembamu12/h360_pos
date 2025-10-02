<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmbededResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('embeded_resources', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->nullable();
            $table->bigInteger('user_id')->index();
            $table->string('name');
            $table->string('original_name');
            $table->string('type');
            $table->text('content');
            $table->text('vector');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('embeded_resources', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::dropIfExists('embeded_resource');
    }
}
