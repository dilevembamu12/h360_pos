<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalReferralPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_referral_payments', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Liaison vers l'hôpital (BusinessLocation) - Mandataire
             $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('restrict');



            // Liaison vers la personne de référence - Mandataire
            $table->foreignId('referral_person_id')->constrained('hospital_referral_persons')->onDelete('restrict'); // Ne pas supprimer le référent si des paiements existent

            // Liaison optionnelle vers le patient si le paiement est lié à un patient spécifique
            $table->foreignId('patient_id')->nullable()->constrained('hospital_patients')->onDelete('set null');

            $table->decimal('amount', 10, 2); // Montant du paiement
            $table->date('payment_date');   // Date du paiement
            $table->text('notes')->nullable(); // Notes sur le paiement

            // Liaison optionnelle vers la transaction de paiement existante (si ce paiement transite par le système de paiement global)
            $table->integer('transaction_payment_id')->nullable()->unsigned();
            $table->foreign('transaction_payment_id')->nullable()->references('id')->on('transaction_payments')->onDelete('set null');
              

            $table->timestamps();
            // Pas de softDeletes généralement pour les enregistrements de paiement

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_referral_payments');
    }
}