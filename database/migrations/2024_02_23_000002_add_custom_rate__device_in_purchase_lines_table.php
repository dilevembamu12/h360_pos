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
        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->decimal('second_currency_rate', 22, 12)->nullable()->after('sub_unit_id')->comment('taux echange devise secondaire');

            $table->integer('second_currency')->unsigned()->nullable()->after('sub_unit_id');
            $table->foreign('second_currency')->references('id')->on('currencies');

         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_lines', function (Blueprint $table) {
            //$table->dropColumn('second_currency_rate');
            //$table->dropColumn('second_currency');
        });
    }
};
