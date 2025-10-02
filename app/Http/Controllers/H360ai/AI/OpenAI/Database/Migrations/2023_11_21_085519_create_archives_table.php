<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            
            $table->BigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->text('title')->nullable();
            $table->longText('content')->nullable();
            $table->longText('filtered_content')->nullable()->default(NULL);
            $table->longText('raw_response')->nullable()->default(NULL);
            $table->string('unique_identifier');
            $table->string('provider')->nullable();
            $table->double('expense')->nullable();
            $table->string('expense_type')->nullable();
            $table->string('type')->comment('code, image, long_article, audio, speech, content etc.');
            $table->string('status')->default('Active');
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
        Schema::dropIfExists('archives');
    }
}
