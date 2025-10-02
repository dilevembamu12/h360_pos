<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalPatientLaboratoryTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_patient_laboratory_tests', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Liaison vers l'hôpital (BusinessLocation) - Mandatoire
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('restrict');



            // Liaison vers le patient - Mandatoire
            $table->foreignId('patient_id')->constrained('hospital_patients')->onDelete('cascade');

            // Liaison vers le type de test (catalogue) - Mandatoire
            $table->integer('laboratory_test_id')->nullable()->unsigned();
            $table->foreign('laboratory_test_id')->nullable()->references('id')->on('users')->onDelete('restrict');
            

            // Liaison vers l'utilisateur (médecin) qui a ordonné le test - Optionnel
            $table->integer('ordered_by_user_id')->nullable()->unsigned();
            $table->foreign('ordered_by_user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            

            // Liaison vers l'utilisateur (technicien labo) qui a effectué/validé le test - Optionnel
            $table->integer('performed_by_user_id')->nullable()->unsigned();
            $table->foreign('performed_by_user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            

            $table->date('order_date'); // Date de la prescription
            $table->date('sample_collection_date')->nullable(); // Date de prélèvement
            $table->date('report_date')->nullable(); // Date de génération du rapport

            // Résultats du test (peut être JSON ou texte simple)
            $table->json('results')->nullable(); // Utiliser JSON pour des résultats structurés
            $table->text('notes')->nullable(); // Notes sur le test ou les résultats

            // Liaison optionnelle vers la visite/admission (OPD/IPD) si le test y est lié
            $table->foreignId('opd_visit_id')->nullable()->constrained('hospital_opd_visits')->onDelete('set null');
            $table->foreignId('ipd_admission_id')->nullable()->constrained('hospital_ipd_admissions')->onDelete('set null');

            // Liaison optionnelle vers la ligne de transaction (facture) pour ce test
            $table->integer('transaction_sell_line_id')->nullable()->unsigned();
            $table->foreign('transaction_sell_line_id', 'hp_lab_test_tsl_fk') // <-- Nom raccourci ici
            ->nullable()->references('id')->on('transaction_sell_lines')->onDelete('set null');


            $table->string('status')->default('ordered'); // ex: 'ordered', 'sample_collected', 'in_progress', 'completed', 'validated', 'cancelled'

            // Ajoutez un champ pour le fichier du rapport si nécessaire (lié au modèle File)
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
        Schema::dropIfExists('hospital_patient_laboratory_tests');
    }
}