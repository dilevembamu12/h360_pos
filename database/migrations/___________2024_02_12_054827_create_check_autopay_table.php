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
        Schema::create('check_autopay', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');



            $table->string('code1')->nullable();
            $table->string('code2')->nullable();


            $table->enum('method', ['flexpay']);
            $table->enum('status', ['received', 'pending', 'ordered', 'draft', 'final']);

            $table->decimal('amount', 22, 4)->default(0);
            $table->string('invoice_no')->nullable();

            $table->text('additional_notes')->nullable();


            $table->dateTime('checked_at')->nullable();

            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();



     
            //Indexing
            $table->index('business_id');
            $table->index('type');
            $table->index('contact_id');
            $table->index('transaction_date');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
