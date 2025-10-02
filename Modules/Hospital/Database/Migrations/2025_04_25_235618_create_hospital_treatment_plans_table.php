<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalTreatmentPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_treatment_plans', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée

            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison obligatoire au patient
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');

            // Liaison obligatoire au dentiste (utilisateur) qui a créé le plan
            $table->integer('dentist_user_id')->nullable()->unsigned();
            $table->foreign('dentist_user_id')->references('id')->on('users')->onDelete('restrict'); // Restrict pour ne pas supprimer l'utilisateur s'il a des plans associés

            $table->date('plan_date'); // Date de création du plan
            $table->string('status')->default('proposed'); // Statut du plan (ex: proposed, accepted, rejected, in_progress, completed)
            $table->text('notes')->nullable(); // Notes générales

            // Montant total estimé (peut être mis à jour à partir des items du plan)
            $table->decimal('total_amount', 20, 4)->default(0.0000);

            // Liaison optionnelle à une transaction de type devis
            $table->integer('quotation_transaction_id')->nullable()->unsigned();
            $table->foreign('quotation_transaction_id')->references('id')->on('transactions')->onDelete('set null');

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
        Schema::dropIfExists('hospital_treatment_plans');
    }
}