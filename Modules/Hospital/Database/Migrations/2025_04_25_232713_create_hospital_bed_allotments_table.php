<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalBedAllotmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_bed_allotments', function (Blueprint $table) {
            $table->id();

            // Liaison multi-tenant
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison vers le patient
            // Assurez-vous que 'create_patients_table' a été exécutée avant.
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');


            // Liaison vers le lit attribué
            // Assurez-vous que 'create_hospital_beds_table' a été exécutée avant.
            $table->foreignId('bed_id')->constrained('hospital_beds')->onDelete('restrict'); // Restrict pour garder l'historique des attributions

            // Liaison vers l'admission IPD (l'hospitalisation concernée)
            // Assurez-vous que 'create_hospital_ipd_admissions_table' a été exécutée avant.
            $table->foreignId('ipd_admission_id')->constrained('hospital_ipd_admissions')->onDelete('cascade'); // Si l'admission est supprimée, l'attribution n'a plus de sens

            $table->dateTime('allotment_time'); // Date et heure d'attribution
            $table->dateTime('discharge_time')->nullable(); // Date et heure de sortie du lit

            $table->text('notes')->nullable();

            // Vous pourriez ajouter un champ pour enregistrer le coût journalier du lit à cette période si le prix peut changer
            // $table->decimal('price_per_day', 10, 2)->nullable();

            $table->timestamps();
            $table->softDeletes(); // Pour les cas où une attribution est enregistrée par erreur et doit être masquée

            // Index pour optimiser les requêtes sur les attributions par lit et par patient
            $table->index(['bed_id', 'patient_id']);
             $table->index('ipd_admission_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_bed_allotments');
    }
}