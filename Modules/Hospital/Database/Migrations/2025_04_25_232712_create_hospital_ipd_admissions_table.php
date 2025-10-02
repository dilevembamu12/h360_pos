<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalIpdAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_ipd_admissions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Multi-tenancy: Link to the business location (Hospital)
             $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');


            // Link to the Patient
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');


            $table->string('ipd_number')->unique()->nullable(); // Unique IPD identifier per hospital/system
            $table->dateTime('admission_date');
            $table->dateTime('discharge_date')->nullable(); // Null if patient is still admitted

            // Link to the current Bed (optional, can change)
            $table->foreignId('current_bed_id')->nullable()->constrained('hospital_beds')->nullOnDelete();

            // Link to the Consultant Doctor (User)
            $table->integer('consultant_doctor_user_id')->nullable()->unsigned();
            $table->foreign('consultant_doctor_user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            
            $table->text('case_details')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->text('discharge_summary')->nullable();
            $table->string('status'); // e.g., 'admitted', 'discharged', 'transferred'

            // Link to the main Transaction/Invoice for this admission
            $table->integer('transaction_id')->nullable()->unsigned();
            $table->foreign('transaction_id')->nullable()->references('id')->on('transactions')->nullOnDelete();


            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // Optional: for soft deletion

            // Add indexes for frequently queried columns
            $table->index('business_location_id');
            $table->index('patient_id');
            $table->index('consultant_doctor_user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_ipd_admissions');
    }
}