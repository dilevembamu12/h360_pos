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
        Schema::create('hospital_generated_certificates', function (Blueprint $table) {
            $table->id(); // Auto-incrementing Primary Key

            // Liaison à l'hôpital/succursale (BusinessLocation)
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison au patient concerné
            $table->unsignedBigInteger('patient_id');
            // >>> CORRECTION ICI : Référence `hospital_patients` au lieu de `business_patients` <<<
            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');
 // Clé étrangère vers la table 'patients' du module Hospital

            // Liaison au modèle de certificat utilisé
            // Nécessite que la table hospital_certificate_templates existe (migration non demandée ici mais nécessaire)
            $table->foreignId('certificate_template_id')->nullable()->constrained('hospital_certificate_templates')->onDelete('set null');

            // Liaison à l'utilisateur qui a émis le certificat
            $table->integer('issued_by_user_id')->nullable()->unsigned();
            $table->foreign('issued_by_user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            
            $table->date('issue_date'); // Date d'émission du certificat
            $table->string('certificate_number')->nullable()->unique(); // Numéro unique du certificat
            $table->text('content')->nullable(); // Contenu du certificat (peut être HTML, texte formaté, ou null si généré en fichier)
            $table->string('file_path')->nullable(); // Chemin vers le fichier du certificat (PDF, etc.) si généré
            $table->string('mime_type')->nullable(); // Type MIME du fichier (si généré)

            $table->timestamps(); // created_at and updated_at
            // Pas de softDeletes généralement pour les certificats émis

            // Index pour la recherche rapide
            // LIGNE À MODIFIER : Ajouter un nom d'index court
            // Ancien : $table->index(['business_location_id', 'issue_date']);
            $table->index(['business_location_id', 'issue_date'], 'hgc_loc_date_idx'); // Nom plus court, ex: 'hgc_loc_date_idx' (Hospital Generated Certificate, Location, Date, Index)

            $table->index('patient_id');
            $table->index('certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_generated_certificates');
    }
};