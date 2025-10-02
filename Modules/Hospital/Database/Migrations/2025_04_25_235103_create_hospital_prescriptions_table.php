<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalPrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_prescriptions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Liaison vers l'hôpital (BusinessLocation) - Mandatoire
             $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('restrict');



            // Liaison vers le patient - Mandataire
            $table->foreignId('patient_id')->constrained('hospital_patients')->onDelete('cascade');

            // Liaison vers l'utilisateur (médecin) qui a prescrit - Mandataire
            $table->integer('doctor_user_id')->nullable()->unsigned();
            $table->foreign('doctor_user_id')->nullable()->references('id')->on('users')->onDelete('restrict');
            


            $table->date('prescription_date'); // Date de l'ordonnance
            $table->text('notes')->nullable(); // Notes générales

            // Liaison optionnelle vers la visite/admission (OPD/IPD) si l'ordonnance y est liée
            $table->foreignId('opd_visit_id')->nullable()->constrained('hospital_opd_visits')->onDelete('set null');
            $table->foreignId('ipd_admission_id')->nullable()->constrained('hospital_ipd_admissions')->onDelete('set null');

            // Liaison optionnelle vers la transaction (facture) si l'ordonnance est facturée globalement
            $table->integer('transaction_id')->nullable()->unsigned();
            $table->foreign('transaction_id')->nullable()->references('id')->on('transactions')->onDelete('set null');
            // Ajoutez un statut si nécessaire (ex: 'active', 'archived')
            // $table->string('status')->default('active');

            $table->timestamps();
            $table->softDeletes(); // Permet de marquer une ordonnance comme non valide/annulée

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_prescriptions');
    }
}