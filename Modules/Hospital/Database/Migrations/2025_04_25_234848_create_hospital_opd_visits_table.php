<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalOpdVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_opd_visits', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Multi-tenancy: Link to the business location (Hospital)
             $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');


            // Link to the Patient
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');


            $table->string('opd_number')->unique()->nullable(); // Unique OPD identifier per day/system
            $table->unsignedBigInteger('appointment_id')->nullable()->constrained('appointments')->nullOnDelete(); // Link to an optional Appointment
            $table->dateTime('visit_date'); // Date and time of the visit

            // Link to the Doctor (User)
            $table->integer('doctor_user_id')->nullable()->unsigned();
            $table->foreign('doctor_user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            

            $table->string('status')->default('in_treatment');

            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('notes')->nullable();
            $table->string('discharge_status'); // e.g., 'in_treatment', 'discharged'

            // Link to the Transaction/Invoice for this visit
            $table->integer('transaction_id')->nullable()->unsigned();
            $table->foreign('transaction_id')->nullable()->references('id')->on('transactions')->nullOnDelete();


            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // Optional: for soft deletion

            // Add indexes for frequently queried columns
            $table->index('business_location_id');
            $table->index('patient_id');
            $table->index('doctor_user_id');
            $table->index('visit_date');
            $table->index('status'); // Index on discharge_status? Or status? Let's use status as per prompt
             $table->index('discharge_status'); // Add index for discharge_status
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_opd_visits');
    }
}