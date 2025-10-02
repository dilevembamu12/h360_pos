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
        Schema::create('hospital_patient_dental_procedures', function (Blueprint $table) {
            $table->id(); // Auto-incrementing Primary Key

            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison obligatoire au patient
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');

            // Liaison obligatoire au type de procédure dentaire réalisée
            $table->unsignedBigInteger('dental_procedure_id');
            $table->foreign('dental_procedure_id')->references('id')->on('hospital_dental_procedures')->onDelete('restrict'); // Restrict pour ne pas supprimer la procédure si elle a été réalisée

            // Liaison optionnelle à l'utilisateur (dentiste) qui a réalisé la procédure
            $table->integer('performed_by_user_id')->nullable()->unsigned();
            $table->foreign('performed_by_user_id')->nullable()->references('id')->on('users')->onDelete('set null');

            $table->date('procedure_date'); // Date de réalisation de la procédure
            $table->text('notes')->nullable(); // Notes spécifiques sur la procédure réalisée

            // Champ pour les surfaces de la dent affectées (si applicable, JSON pour flexibilité)
            $table->json('affected_surfaces')->nullable();

            // Liaison optionnelle à la ligne de transaction (facture) si cette procédure est facturée individuellement
            $table->integer('transaction_sell_line_id')->nullable()->unsigned();
            $table->foreign('transaction_sell_line_id', 'hpdp_tsl_fk') // <-- Nom raccourci pour la clé étrangère si besoin
                  ->nullable()->references('id')->on('transaction_sell_lines')->onDelete('set null');

            // Statut de la procédure (ex: 'completed', 'cancelled')
            $table->string('status')->default('completed');


            $table->timestamps(); // created_at et updated_at
            $table->softDeletes(); // deleted_at

            // Index pour la recherche rapide
            // SPÉCIFICATION D'UN NOM COURT POUR ÉVITER L'ERREUR DE LONGUEUR
            $table->index(['business_location_id', 'patient_id'], 'hpdp_loc_pat_idx'); // Nom d'index court
            $table->index('procedure_date');
             $table->index('dental_procedure_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_patient_dental_procedures');
    }
};