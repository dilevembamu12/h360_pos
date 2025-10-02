<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalAllergiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_allergies', function (Blueprint $table) {
            $table->id();   


            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');


            // Liaison vers le patient
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');

            
            $table->string('name'); // Nom de l'allergie (ex: Pénicilline, Arachide)
            $table->text('reaction')->nullable(); // Description de la réaction
            $table->text('notes')->nullable();    // Notes supplémentaires

            $table->timestamps();
            $table->softDeletes(); // Pour suppression logique
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_allergies');
    }
}