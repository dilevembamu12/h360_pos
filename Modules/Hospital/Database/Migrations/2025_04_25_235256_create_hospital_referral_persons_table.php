<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalReferralPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_referral_persons', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée

            // Liaison obligatoire à la Business Location
           $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');


            $table->string('name'); // Nom de la personne ou organisation de référence
            $table->string('contact_person')->nullable(); // Nom de la personne contact (si organisation)
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable(); // Notes (ex: termes de parrainage, accords)

            $table->timestamps(); // created_at et updated_at
            $table->softDeletes(); // deleted_at pour suppression logique
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_referral_persons');
    }
}