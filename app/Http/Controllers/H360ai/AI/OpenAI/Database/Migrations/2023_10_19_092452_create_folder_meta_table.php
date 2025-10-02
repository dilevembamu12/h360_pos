<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFolderMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folder_id');
            $table->string('key')->index();
            $table->string('value', 10000)->nullable();
            $table->unique(['key', 'folder_id']);

            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('folder_meta', function (Blueprint $table) {
            $table->dropIndex(['folder_id', 'key']);
        });
        Schema::dropIfExists('folder_meta');
    }
}
