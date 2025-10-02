<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalToothEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_tooth_entries', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée

            // Liaison obligatoire à l'odontogramme (DentalChart)
            $table->unsignedBigInteger('dental_chart_id');
            $table->foreign('dental_chart_id')->references('id')->on('hospital_dental_charts')->onDelete('cascade');

            $table->string('tooth_identifier'); // Identifiant de la dent (ex: "16", "53")
            $table->date('entry_date'); // Date de l'observation ou de la procédure enregistrée

            $table->string('type'); // Type d'entrée ('condition', 'procedure_done', 'procedure_planned', 'missing', etc.)
            $table->string('condition_name')->nullable(); // Nom de la condition (si type='condition')

            // Liaison optionnelle au type de procédure (si type='procedure_done' ou 'procedure_planned')
            $table->unsignedBigInteger('dental_procedure_id')->nullable();
            $table->foreign('dental_procedure_id')->references('id')->on('hospital_dental_procedures')->onDelete('set null');

            $table->string('status')->nullable(); // Statut de l'entrée (ex: 'existing', 'completed', 'planned')
            $table->text('notes')->nullable(); // Notes spécifiques

            // Champ pour les surfaces de la dent affectées (si applicable, JSON pour flexibilité)
            $table->json('surfaces')->nullable();

            // Liaison optionnelle à la procédure patient concrète si cette entrée la représente
            $table->unsignedBigInteger('patient_dental_procedure_id')->nullable();
            $table->foreign('patient_dental_procedure_id', 'tooth_entry_pat_dent_proc_fk') // Nom FK plus court si besoin
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
        Schema::dropIfExists('hospital_tooth_entries');
    }
}