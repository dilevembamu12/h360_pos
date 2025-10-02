<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFolderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folder_id');
            $table->BigInteger('item_id');
            $table->string('item_type', 191);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

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
        Schema::table('folder_items', function (Blueprint $table) {
            $table->dropIndex(['folder_id']);
        });
        Schema::dropIfExists('folder_items');
    }
}
