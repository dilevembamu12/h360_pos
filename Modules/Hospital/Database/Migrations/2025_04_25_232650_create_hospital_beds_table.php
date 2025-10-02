<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalBedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_beds', function (Blueprint $table) {
            $table->id();

            // Liaison multi-tenant
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            $table->string('bed_number');   // Numéro ou identifiant du lit
            $table->string('room_number')->nullable(); // Numéro de chambre (si applicable)
            $table->string('ward')->nullable();     // Service/Aile (ex: Pédiatrie, Chirurgie)
            $table->string('bed_type')->nullable(); // Type de lit (ex: Standard, Intensifs, Chambre seule)
            $table->string('status')->default('available'); // ex: available, occupied, maintenance, cleaning

            // Liaison optionnelle vers un item d'inventaire (si les lits sont gérés comme des actifs dans l'inventaire)
            // Assurez-vous que la table 'im_inventory_items' du module InventoryManagement existe.
            // $table->foreignId('inventory_item_id')->nullable()->constrained('im_inventory_items')->onDelete('set null');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Pour les lits qui sont retirés du service ou déclassés

            // Index unique pour garantir l'unicité des lits par Business Location
            $table->unique(['business_location_id', 'bed_number', 'room_number']); // Unité peut varier
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_beds');
    }
}