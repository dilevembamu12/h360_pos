<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalAmbulanceCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_ambulance_calls', function (Blueprint $table) {
            $table->id();

            // Liaison multi-tenant
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison vers l'ambulance utilisée
            // Assurez-vous que 'create_hospital_ambulances_table' a été exécutée avant.
            $table->foreignId('ambulance_id')->constrained('hospital_ambulances')->onDelete('restrict'); // Restrict si l'ambulance est en service

            // Liaison optionnelle vers le patient (peut être pour une urgence non identifiée immédiatement)
            // Assurez-vous que 'create_patients_table' a été exécutée avant.
            // Liaison vers le patient
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');

            // Liaison vers le chauffeur (utilisateur)
            // Assurez-vous que la table 'users' existe.
            // Liaison obligatoire à la Business Location
            $table->integer('driver_user_id')->nullable()->unsigned();
            $table->foreign('driver_user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            


            $table->dateTime('call_time');        // Heure de l'appel
            $table->string('pickup_location');  // Lieu de prise en charge
            $table->string('drop_location')->nullable(); // Lieu de destination (hôpital, autre, etc.)
            $table->double('distance')->nullable(); // Distance parcourue
            $table->decimal('charges', 10, 2)->nullable(); // Frais du transport

            // Liaison optionnelle vers la ligne de facture si le transport est facturé
            // Assurez-vous que la table 'transaction_sell_lines' existe.
            $table->integer('transaction_sell_line_id')->nullable()->unsigned();
            $table->foreign('transaction_sell_line_id')->nullable()->references('id')->on('transaction_sell_lines')->onDelete('set null');
            


            $table->text('notes')->nullable();

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
        Schema::dropIfExists('hospital_ambulance_calls');
    }
}