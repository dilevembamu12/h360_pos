<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalNurseNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_nurse_notes', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Multi-tenancy: Link to the business location (Hospital)
             $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');


            // Link to the Patient
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');


            // Link to the specific IPD Admission
            $table->foreignId('ipd_admission_id')->constrained('hospital_ipd_admissions')->cascadeOnDelete();

            // Link to the Nurse (User) who recorded the note
            $table->integer('recorded_by_user_id')->nullable()->unsigned();
            $table->foreign('recorded_by_user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();

            $table->date('note_date');
            $table->time('note_time');
            $table->text('notes'); // The actual content of the nurse note

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // Optional: for soft deletion

            // Add indexes for frequently queried columns
            $table->index('business_location_id');
            $table->index('patient_id');
            $table->index('ipd_admission_id');
            $table->index('recorded_by_user_id');
            $table->index('note_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_nurse_notes');
    }
}