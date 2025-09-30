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

        Schema::create('mobilemoney_pay_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currencies');

            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            
            $table->string('order_number')->nullable();
            $table->string('payment_ref')->nullable();
            

            $table->decimal('amount', 22, 4)->default(0);

            $table->enum('method', ['flexpay']);
            $table->enum('status', ['received', 'pending', 'canceled', 'draft', 'final', 'failed']);

            $table->string('mobile');

            $table->text('additional_notes')->nullable();


            $table->dateTime('checked_at')->nullable();
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
        Schema::dropIfExists('mobilemoney_pay_lines');
    }
};
