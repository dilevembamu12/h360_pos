<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalLaboratoryTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_laboratory_tests', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Liaison optionnelle vers l'hôpital (BusinessLocation) si les tests varient par hôpital
            //$table->foreignId('business_location_id')->nullable()->constrained('business_locations')->onDelete('set null');
            

            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');



            $table->string('name'); // Nom du test (ex: NFS)
            $table->string('short_name')->nullable(); // Nom court/code
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0); // Prix par défaut
            // Ajoutez d'autres colonnes pertinentes (unité de mesure, valeurs de référence par défaut, etc.)

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
        Schema::dropIfExists('hospital_laboratory_tests');
    }
}