<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeOnChatBots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_bots', function (Blueprint $table) {
            $table->unsignedBigInteger('chat_category_id')->nullable()->change();
            $table->bigInteger('user_id')->nullable()->after('chat_category_id');
            $table->string('type', 191)->nullable()->after('status');
            $table->text('message')->nullable()->change();
            $table->string('role')->nullable()->change();
            $table->unique('code');
            $table->text('promt')->nullable()->change();

            $table->softDeletes();
            $table->unique(['user_id', 'name', 'deleted_at']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_bots', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropUnique(['user_id', 'name', 'deleted_at']);
        });
    }
}
