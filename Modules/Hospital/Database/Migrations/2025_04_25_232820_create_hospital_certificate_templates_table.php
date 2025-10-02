<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalCertificateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_certificate_templates', function (Blueprint $table) {
            $table->id();

            // Liaison multi-tenant
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            $table->string('name');     // Nom du modèle (ex: Certificat de naissance, Carte Patient)
            $table->string('type');     // Type de certificat (ex: birth, death, patient_id, staff_id, custom_patient, custom_staff)
            $table->longText('template_html'); // Le contenu HTML du modèle, avec des placeholders pour les données
            $table->boolean('is_active')->default(true); // Si le modèle est utilisable
            $table->text('notes')->nullable(); // Description du modèle

            // Vous pourriez ajouter des champs pour les dimensions ou le format (A4, carte, etc.)
            // $table->string('format')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Pour archiver des modèles devenus obsolètes

            // Assurer l'unicité du nom du modèle par Business Location et type
            // -- CORRECTION ICI --
            // On spécifie un nom plus court pour l'index unique
            // Le nom auto-généré était trop long.
            $table->unique(['business_location_id', 'name', 'type'], 'hosp_cert_loc_name_type_unique'); // Nom raccourci

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_certificate_templates');
    }
}