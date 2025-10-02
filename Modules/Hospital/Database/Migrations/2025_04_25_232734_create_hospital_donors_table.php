<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_donors', function (Blueprint $table) {
            $table->id(); // Auto-incrementing Primary Key

            // Liaison à l'hôpital/succursale (BusinessLocation)
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            $table->string('donor_id')->nullable()->unique(); // Identifiant interne du donneur (optionnel)
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable(); // 'male', 'female', 'other'
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('blood_group')->nullable(); // Ex: 'A+', 'B-', 'O+'
            $table->date('last_donation_date')->nullable();
            $table->text('notes')->nullable();
            // Ajouter d'autres champs pertinents pour un donneur (état de santé, etc.)

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at

             // Index pour la recherche rapide
             $table->index(['business_location_id', 'blood_group']);
             $table->index(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_donors');
    }
};