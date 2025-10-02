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
        Schema::create('hospital_files', function (Blueprint $table) {
            $table->id(); // Auto-incrementing Primary Key

            // Liaison à l'hôpital/succursale (BusinessLocation)
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison polymorphique à l'entité parente (Patient, OpdVisit, IpdAdmission, etc.)
            $table->morphs('related_entity'); // Ajoute related_entity_id (unsignedBigInteger) et related_entity_type (string)

            // Liaison à l'utilisateur qui a uploadé le fichier
            $table->integer('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            

            $table->string('file_name'); // Nom original du fichier
            $table->string('file_path'); // Chemin de stockage du fichier
            $table->unsignedBigInteger('file_size')->nullable(); // Taille du fichier en octets
            $table->string('mime_type')->nullable(); // Type MIME du fichier
            $table->text('description')->nullable(); // Description du fichier

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at

            // Index pour la recherche rapide
            $table->index('business_location_id');
            $table->index(['related_entity_id', 'related_entity_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_files');
    }
};