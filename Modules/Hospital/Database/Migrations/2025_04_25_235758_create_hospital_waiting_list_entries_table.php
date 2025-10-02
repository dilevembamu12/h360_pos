<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalWaitingListEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_waiting_list_entries', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée

            // Liaison obligatoire à la Business Location
            $table->unsignedInteger('business_location_id');
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison obligatoire au patient
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');

            $table->string('queue_type'); // Type de file (ex: 'reception_general', 'pharmacy', 'lab_pickup', 'opd_doctor_X')
            $table->integer('daily_queue_number'); // Numéro dans la file pour la journée

            // Assurer l'unicité du numéro de file par type et par jour pour une business location
            $table->unique(['business_location_id', 'queue_type', 'daily_queue_number', 'entry_time'], 'unique_daily_queue_number');
            // Note: L'ajout de entry_time dans l'index unique est une astuce pour gérer les cas où la file est reset ou si plusieurs entries ont le même numéro très très rarement, mais un simple index sur [business_location_id, queue_type, daily_queue_number] avec une logique d'incrémentation par jour est généralement suffisant pour l'unicité fonctionnelle. Pour une unicité stricte gérée par la DB, l'entry_time (ou juste la date de entry_time) est nécessaire dans l'index.

            $table->dateTime('entry_time'); // Date et heure d'entrée dans la file

            // Statut de l'entrée
            $table->string('status')->default('waiting'); // ex: 'waiting', 'called', 'serving', 'completed', 'cancelled', 'no_show'

            $table->dateTime('called_time')->nullable(); // Date et heure où le patient a été appelé
            $table->dateTime('serving_time')->nullable(); // Date et heure où le service a commencé
            $table->dateTime('completion_time')->nullable(); // Date et heure où le service s'est terminé
            $table->dateTime('cancelled_time')->nullable(); // Date et heure où l'entrée a été annulée

            // Liaison optionnelle à l'utilisateur qui a annulé ou géré l'entrée
            $table->unsignedInteger('cancelled_by_user_id')->nullable();
            $table->foreign('cancelled_by_user_id')->references('id')->on('users')->onDelete('set null');

             $table->unsignedInteger('managed_by_user_id')->nullable();
            $table->foreign('managed_by_user_id')->references('id')->on('users')->onDelete('set null');


            $table->text('notes')->nullable(); // Notes sur l'entrée

            // Colonnes pour la relation polymorphique (lier à un Rendez-vous, une Visite OPD, etc.)
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->string('related_entity_type')->nullable();
            // Pas de clé étrangère pour les relations polymorphiques au niveau DB

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
        Schema::dropIfExists('hospital_waiting_list_entries');
    }
}