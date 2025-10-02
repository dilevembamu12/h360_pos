<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalOperativeProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_operative_procedures', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Multi-tenancy: Link to the business location (Hospital)
             $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');


            // Link to the Patient
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');


            // Link to the specific IPD Admission (procedure often happens during IPD)
            $table->foreignId('ipd_admission_id')->constrained('hospital_ipd_admissions')->cascadeOnDelete();

            $table->date('procedure_date');
            $table->time('procedure_time')->nullable();
            $table->string('procedure_name'); // Name of the procedure (e.g., Appendectomy)
            $table->text('description')->nullable(); // Detailed description of the procedure performed

            // Link to the main Surgeon (User)
            $table->integer('surgeon_user_id')->nullable()->unsigned();
            $table->foreign('surgeon_user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            

            // Optional Links to other staff (Users)
            $table->integer('anesthetist_user_id')->nullable()->unsigned();
            $table->foreign('anesthetist_user_id')->nullable()->references('id')->on('users')->nullOnDelete();
            

            // JSON fields for multiple staff members involved
            $table->json('assistant_surgeons_users_ids')->nullable(); // Store array of user IDs
            $table->json('nurses_users_ids')->nullable(); // Store array of user IDs
            // You could also link to StaffProfile IDs if needed, but User ID might be sufficient

            $table->text('notes')->nullable(); // General notes about the procedure
            $table->text('complications')->nullable(); // Any complications encountered
            $table->string('status'); // e.g., 'scheduled', 'in_progress', 'completed', 'cancelled'

            // Link to the TransactionSellLine for billing the procedure
            $table->integer('transaction_sell_line_id')->nullable()->unsigned();
            $table->foreign('transaction_sell_line_id')->nullable()->references('id')->on('transaction_sell_lines')->nullOnDelete();
            
            
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // Optional: for soft deletion

            // Add indexes for frequently queried columns
            $table->index('business_location_id');
            $table->index('patient_id');
            $table->index('ipd_admission_id');
            $table->index('surgeon_user_id');
            $table->index('procedure_date');
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
        Schema::dropIfExists('hospital_operative_procedures');
    }
}