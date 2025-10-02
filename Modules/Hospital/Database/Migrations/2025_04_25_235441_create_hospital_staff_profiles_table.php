<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalStaffProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_staff_profiles', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée

            // Liaison obligatoire à l'utilisateur (app\User)
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Assurez-vous que chaque utilisateur n'a qu'un seul profil par Business Location (si besoin)
            // $table->unique(['user_id', 'business_location_id'], 'user_business_location_unique');


            // Liaison obligatoire à la Business Location (hôpital)
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            $table->string('hospital_staff_id')->nullable()->unique(); // Identifiant interne du personnel pour cet hôpital
            $table->date('date_of_joining'); // Date d'entrée dans cet hôpital
            $table->string('status')->default('active'); // Statut du personnel (ex: active, inactive)

            // Liaison optionnelle à la spécialité (pour les médecins/infirmières spécialisés)
            $table->unsignedBigInteger('specialist_id')->nullable();
            $table->foreign('specialist_id')->references('id')->on('hospital_specialists')->onDelete('set null');

            // Ajoutez d'autres champs spécifiques au profil hospitalier si nécessaire
            // $table->string('medical_registration_number')->nullable();

            $table->timestamps(); // created_at et updated_at
            // Pas de softDeletes par défaut pour les profils, la suppression de l'User gère ça.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_staff_profiles');
    }
}