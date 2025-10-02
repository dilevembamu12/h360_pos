<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalTreatmentPlanItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_treatment_plan_items', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée

            // Liaison obligatoire au plan de traitement parent
            $table->unsignedBigInteger('treatment_plan_id');
            $table->foreign('treatment_plan_id')->references('id')->on('hospital_treatment_plans')->onDelete('cascade');

            // Liaison obligatoire au type de procédure dentaire planifiée
            $table->unsignedBigInteger('dental_procedure_id');
            $table->foreign('dental_procedure_id')->references('id')->on('hospital_dental_procedures')->onDelete('restrict'); // Restrict pour ne pas supprimer la procédure si elle est planifiée

            $table->string('tooth_identifier')->nullable(); // Identifiant de la dent concernée (si applicable)
            $table->text('notes')->nullable(); // Notes spécifiques pour cet élément du plan

            $table->string('status')->default('planned'); // Statut de l'élément (ex: planned, completed, deferred, cancelled)

            $table->decimal('estimated_price', 20, 4)->nullable(); // Prix estimé pour cette étape

            $table->date('completion_date')->nullable(); // Date estimée ou réelle de réalisation

            // Liaison optionnelle si cet élément du plan a été réalisé et a généré un PatientDentalProcedure
            $table->unsignedBigInteger('patient_dental_procedure_id')->nullable();
            $table->foreign('patient_dental_procedure_id', 'tp_item_pat_dent_proc_fk') // Nom FK plus court si besoin
                  ->references('id')->on('hospital_patient_dental_procedures')->onDelete('set null');

            $table->timestamps(); // created_at et updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_treatment_plan_items');
    }
}