<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_appointments', function (Blueprint $table) {
            $table->id();

            // Liaison multi-tenant
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison vers le patient
            // Assurez-vous que 'create_patients_table' a été exécutée avant.
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');


            // Liaison vers le médecin (utilisateur)
            // Assurez-vous que la table 'users' existe.
            $table->integer('doctor_user_id')->nullable()->unsigned();
            $table->foreign('doctor_user_id')->nullable()->references('id')->on('users')->onDelete('restrict');// Restrict car le médecin est responsable

            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('status')->default('pending'); // ex: pending, confirmed, completed, cancelled, no_show
            $table->string('appointment_type')->nullable(); // ex: 'online', 'offline'
            // Liaison optionnelle vers la consultation en direct si c'est un rendez-vous en ligne
            // Assurez-vous que 'create_hospital_live_consultations_table' a été exécutée avant.
            $table->foreignId('live_consultation_id')->nullable()->constrained('hospital_live_consultations')->onDelete('set null');

            $table->text('notes')->nullable();
            $table->decimal('amount', 10, 2)->nullable(); // Frais du rendez-vous/consultation
            $table->string('payment_status')->default('unpaid'); // ex: unpaid, paid, partial

            // Liaison optionnelle vers la transaction (facture) si le rendez-vous est facturé directement
            // Assurez-vous que la table 'transactions' existe.
            $table->integer('transaction_id')->nullable()->unsigned();
            $table->foreign('transaction_id')->nullable()->references('id')->on('transactions')->onDelete('set null');

            $table->timestamps();
            // Pas de softDeletes pour les rendez-vous annulés, le statut suffit, mais peut être ajouté si historique complet nécessaire.
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_appointments');
    }
}