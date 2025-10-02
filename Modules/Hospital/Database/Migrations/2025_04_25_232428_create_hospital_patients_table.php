<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_patients', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (BigInt)

            // Liaison vers l'hôpital (BusinessLocation) - Mandatoire pour le multi-tenancy
             $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('restrict');



            $table->string('record_id')->unique()->nullable(); // Identifiant unique du dossier patient
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable(); // ex: 'male', 'female', 'other'
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('patient_type')->nullable(); // ex: 'outpatient', 'inpatient'
            $table->string('blood_group')->nullable(); // ex: 'A+', 'B-', 'O+'
            // Ajoutez d'autres colonnes pertinentes selon le besoin (photo, statut marital, profession, contact d'urgence, etc.)

            $table->timestamps(); // created_at and updated_at
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
        Schema::dropIfExists('hospital_patients');
    }
}