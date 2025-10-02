<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalLiveConsultationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_live_consultations', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Multi-tenancy: Link to the business location (Hospital)
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->cascadeOnDelete();


            // Link to the Patient
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->cascadeOnDelete();


            // Link to the Doctor (User)
            $table->integer('doctor_user_id')->nullable()->unsigned();
            $table->foreign('doctor_user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            
            $table->dateTime('consultation_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('platform')->nullable(); // e.g., 'Zoom', 'Google Meet'
            $table->string('platform_meeting_id')->nullable();
            $table->string('platform_meeting_url')->nullable();
            $table->string('status'); // e.g., 'scheduled', 'started', 'completed', 'cancelled'
            $table->text('notes')->nullable();

            // Colonnes pour la relation polymorphique (lier à un Rendez-vous, une Visite OPD, une Admission IPD)
            // ATTENTION : Définition manuelle pour court-circuiter le nom d'index trop long
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->string('related_entity_type')->nullable();

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // Added SoftDeletes based on previous pattern

            // Add indexes for frequently queried columns
            $table->index('business_location_id');
            $table->index('patient_id');
            $table->index('doctor_user_id');
            $table->index('status');
            
            // *** FIX : Spécifier un nom d'index plus court ***
            $table->index(['related_entity_id', 'related_entity_type'], 'live_consults_entity_index'); // Nom court
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_live_consultations');
    }
}