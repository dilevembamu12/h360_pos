<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullOnEmbededResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('embeded_resources', function (Blueprint $table) {
            $table->longText('content')->change();
            $table->text('vector')->nullable()->change();
            $table->string('category')->nullable()->comment('widgetChatBot etc.')->after('vector');
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

        });
    }
}
