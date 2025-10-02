<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalRadiologyTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_radiology_tests', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Liaison optionnelle vers l'hôpital (BusinessLocation) si les tests varient par hôpital
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->nullable()->references('id')->on('business_locations')->onDelete('cascade');


            $table->string('name'); // Nom du test (ex: Radio X-ray thorax)
            $table->string('short_name')->nullable(); // Nom court/code
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0); // Prix par défaut
            // Ajoutez d'autres colonnes pertinentes (parties du corps, préparation requise, etc.)

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_radiology_tests');
    }
}