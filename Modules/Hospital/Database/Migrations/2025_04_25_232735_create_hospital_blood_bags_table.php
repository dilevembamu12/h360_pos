<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalBloodBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_blood_bags', function (Blueprint $table) {
            $table->id();

            // Liaison multi-tenant
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison vers le donneur
            // Assurez-vous que 'create_hospital_donors_table' a été exécutée avant.
            $table->foreignId('donor_id')->constrained('hospital_donors')->onDelete('restrict'); // On garde la poche même si le donneur est masqué/supprimé logiquement

            $table->string('bag_number')->unique()->nullable(); // Numéro de poche unique
            $table->string('blood_group'); // Groupe sanguin (ex: A+, B-, O-, AB+)
            $table->string('component')->nullable(); // Composant (ex: Sang total, Culot globulaire, Plasma, Plaquettes)
            $table->date('collection_date'); // Date de prélèvement
            $table->date('expiry_date');     // Date d'expiration
            $table->string('status')->default('available'); // ex: available, issued, tested, discarded, expired

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Pour les poches jetées ou expirées

            // Index pour une recherche rapide par groupe sanguin et statut
            $table->index(['blood_group', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_blood_bags');
    }
}