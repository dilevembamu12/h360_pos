<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
             /**** custom  */
             $table->integer('box_unit_id')->unsigned()->nullable();
             $table->foreign('box_unit_id')->references('id')->on('units');
             /************************* */

            //************custom */
            $table->index('box_unit_id');
            /************************* */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
