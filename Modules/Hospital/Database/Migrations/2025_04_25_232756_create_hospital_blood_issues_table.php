<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalBloodIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_blood_issues', function (Blueprint $table) {
            $table->id();

            // Liaison multi-tenant
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison vers le patient receveur
            // Assurez-vous que 'create_patients_table' a été exécutée avant.
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');


            // Liaison vers la poche de sang délivrée
            // Assurez-vous que 'create_hospital_blood_bags_table' a été exécutée avant.
            $table->foreignId('blood_bag_id')->constrained('hospital_blood_bags')->onDelete('restrict'); // Restrict car on doit garder la trace de quelle poche a été donnée

            // Liaison vers l'utilisateur qui a délivré le sang
            // Assurez-vous que la table 'users' existe.
            $table->integer('issued_by_user_id')->nullable()->unsigned();
            $table->foreign('issued_by_user_id')->nullable()->references('id')->on('users')->onDelete('restrict');
            

            $table->dateTime('issue_date'); // Date et heure de délivrance

            // Liaison optionnelle vers la ligne de facture si la transfusion est facturée
            // Assurez-vous que la table 'transaction_sell_lines' existe.
            $table->integer('transaction_sell_line_id')->nullable()->unsigned();
            $table->foreign('transaction_sell_line_id')->nullable()->references('id')->on('transaction_sell_lines')->onDelete('set null');
            

            $table->text('notes')->nullable(); // Raison de la transfusion, notes sur l'administration, etc.

            $table->timestamps();
            $table->softDeletes(); // Pour les enregistrements faits par erreur

            // Index pour une recherche rapide par patient et poche de sang
            $table->index(['patient_id', 'blood_bag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_blood_issues');
    }
}