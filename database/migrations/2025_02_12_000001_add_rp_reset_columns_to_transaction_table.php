<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**   personnalize custom code 12022025-RESET REWARD ***/
            //fonctionalité pour reinitialiser les points bonus
            /******************************************************* */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('rp_reset')->default(false)->after('rp_earned');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
