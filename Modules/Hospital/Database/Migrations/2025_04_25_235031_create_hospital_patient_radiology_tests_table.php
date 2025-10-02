<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalPatientRadiologyTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_patient_radiology_tests', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Liaison vers l'hôpital (BusinessLocation) - Mandatoire
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('restrict');


            // Liaison vers le patient - Mandataire
            $table->foreignId('patient_id')->constrained('hospital_patients')->onDelete('cascade');

            // Liaison vers le type de test (catalogue) - Mandataire
            $table->foreignId('radiology_test_id')->constrained('hospital_radiology_tests')->onDelete('restrict');

            // Liaison vers l'utilisateur (médecin) qui a ordonné le test - Optionnel
            $table->integer('ordered_by_user_id')->nullable()->unsigned();
            $table->foreign('ordered_by_user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            

            // Liaison vers l'utilisateur (technicien radio) qui a effectué/validé le test - Optionnel
            $table->integer('performed_by_user_id')->nullable()->unsigned();
            $table->foreign('performed_by_user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            
            $table->date('order_date'); // Date de la prescription
            $table->date('report_date')->nullable(); // Date de génération du rapport

            // Résultats ou conclusion du rapport
            $table->text('results')->nullable(); // Texte du rapport ou conclusion
            $table->text('notes')->nullable(); // Notes additionnelles

            // Liaison optionnelle vers la visite/admission (OPD/IPD) si le test y est lié
            $table->foreignId('opd_visit_id')->nullable()->constrained('hospital_opd_visits')->onDelete('set null');
            $table->foreignId('ipd_admission_id')->nullable()->constrained('hospital_ipd_admissions')->onDelete('set null');

             // Liaison optionnelle vers la ligne de transaction (facture) pour ce test
            $table->integer('transaction_sell_line_id')->nullable()->unsigned();
            $table->foreign('transaction_sell_line_id', 'hosp_pat_rad_tsl_fk')->nullable()->references('id')->on('transaction_sell_lines')->onDelete('set null');
            

            $table->string('status')->default('ordered'); // ex: 'ordered', 'in_progress', 'completed', 'validated', 'cancelled'

            // Ajoutez un champ pour le fichier de l'image/du rapport si nécessaire (lié au modèle File)
            // $table->foreignId('report_file_id')->nullable()->constrained('hospital_files')->onDelete('set null');

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
        Schema::dropIfExists('hospital_patient_radiology_tests');
    }
}