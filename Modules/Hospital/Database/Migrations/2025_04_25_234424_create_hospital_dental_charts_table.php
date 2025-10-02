<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_dental_charts', function (Blueprint $table) {
            $table->id(); // Auto-incrementing Primary Key

            // Liaison à l'hôpital/succursale (BusinessLocation)
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison au patient
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');
 // Clé étrangère vers la table 'patients' du module Hospital

            $table->string('chart_type')->nullable(); // Ex: 'Adult', 'Pediatric'
            $table->string('tooth_numbering_system')->nullable(); // Ex: 'FDI', 'Universal'
            
            $table->integer('last_updated_by_user_id')->nullable()->unsigned();
            $table->foreign('last_updated_by_user_id')->nullable()->references('id')->on('users')->onDelete('set null');// Liaison à l'utilisateur standard

            $table->timestamp('last_updated_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at

            // Index pour la recherche rapide
            $table->index(['business_location_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_dental_charts');
    }
};