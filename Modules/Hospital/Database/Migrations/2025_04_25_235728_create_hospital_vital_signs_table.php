<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalVitalSignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_vital_signs', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée

            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison obligatoire au patient
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');

            // Liaison optionnelle à une admission IPD si la prise de vitaux est dans ce contexte
            $table->unsignedBigInteger('ipd_admission_id')->nullable();
            $table->foreign('ipd_admission_id')->references('id')->on('hospital_ipd_admissions')->onDelete('cascade');

            // Liaison obligatoire à l'utilisateur (infirmière/médecin) qui a enregistré les vitaux
            $table->unsignedInteger('recorded_by_user_id');
            $table->foreign('recorded_by_user_id')->references('id')->on('users')->onDelete('restrict');

            $table->dateTime('recorded_at'); // Date et heure de l'enregistrement

            $table->decimal('temperature', 5, 2)->nullable(); // Température (ex: 37.5)
            $table->integer('pulse_rate')->nullable(); // Pouls (ex: 75 bpm)
            $table->integer('blood_pressure_systolic')->nullable(); // Tension systolique (ex: 120)
            $table->integer('blood_pressure_diastolic')->nullable(); // Tension diastolique (ex: 80)
            $table->integer('respiration_rate')->nullable(); // Fréquence respiratoire (ex: 16 rpm)
            $table->decimal('oxygen_saturation', 5, 2)->nullable(); // Saturation O2 (ex: 98.5)
            $table->decimal('height', 10, 2)->nullable(); // Taille (ex: 175.5 cm)
            $table->decimal('weight', 10, 2)->nullable(); // Poids (ex: 70.2 kg)
            $table->decimal('bmi', 10, 2)->nullable(); // IMC (peut être calculé ou stocké)

            $table->text('notes')->nullable(); // Notes additionnelles

            $table->timestamps(); // created_at et updated_at (recorded_at est distinct)
            // Pas de softDeletes par défaut pour les vitaux
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_vital_signs');
    }
}