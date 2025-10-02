<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalAmbulancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_ambulances', function (Blueprint $table) {
            $table->id();

            // Liaison multi-tenant
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            $table->string('vehicle_number')->unique(); // Numéro d'immatriculation ou identifiant unique
            $table->string('vehicle_model')->nullable();
            $table->string('driver_name')->nullable(); // Nom du chauffeur par défaut ou dernier chauffeur
            $table->string('driver_contact')->nullable(); // Contact du chauffeur
            $table->string('status')->default('available'); // ex: available, en_route, in_maintenance
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('hospital_ambulances');
    }
}