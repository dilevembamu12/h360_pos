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
        Schema::create('hospital_dental_procedures', function (Blueprint $table) {
            $table->id(); // Auto-incrementing Primary Key

            // Liaison à l'hôpital/succursale (BusinessLocation) - Optionnel selon la portée des procédures
            // Si les procédures sont spécifiques par hôpital, décommenter la ligne suivante:
            // $table->foreignId('business_location_id')->nullable()->constrained('business_locations')->onDelete('cascade');

            $table->string('code')->nullable()->unique(); // Code de la procédure (ex: D1110), peut être nullable si non standardisé
            $table->string('name'); // Nom de la procédure (ex: Nettoyage prothétique)
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0); // Prix par défaut

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at (pour suppression logique)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_dental_procedures');
    }
};