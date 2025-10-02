<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalSpecialistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_specialists', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée

            // Liaison optionnelle à la Business Location si les spécialités sont spécifiques par hôpital
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            $table->string('name'); // Nom de la spécialité (ex: Cardiologie, Pédiatrie)
            $table->text('description')->nullable(); // Description de la spécialité

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
        Schema::dropIfExists('hospital_specialists');
    }
}