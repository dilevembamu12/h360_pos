<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalMedicalHistoryEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_medical_history_entries', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Multi-tenancy: Link to the business location (Hospital)
             $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');


            // Link to the Patient
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');


            $table->date('entry_date');
            $table->string('entry_type'); // e.g., 'condition', 'surgery', 'vaccination', 'family_history', 'allergy'
            $table->string('summary')->nullable(); // A brief summary of the entry
            $table->text('details')->nullable(); // More detailed description

            // Link to the User who recorded the entry
            $table->integer('recorded_by_user_id')->nullable()->unsigned();
            $table->foreign('recorded_by_user_id')->nullable()->references('id')->on('users')->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // Optional: for soft deletion

            // Add indexes for frequently queried columns
            $table->index('business_location_id');
            $table->index('patient_id');
            $table->index('entry_type');
            $table->index('entry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_medical_history_entries');
    }
}