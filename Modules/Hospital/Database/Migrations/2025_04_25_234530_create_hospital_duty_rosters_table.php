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
        Schema::create('hospital_duty_rosters', function (Blueprint $table) {
            $table->id(); // Auto-incrementing Primary Key

            // Liaison à l'hôpital/succursale (BusinessLocation)
            // Liaison obligatoire à la Business Location
            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Liaison à l'utilisateur (membre du personnel)
            $table->integer('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            

            // Liaison au shift (depuis le module Essentials)
            // Assurez-vous que la table essentials_shifts existe
            $table->integer('shift_id')->nullable()->unsigned();
            $table->foreign('shift_id')->nullable()->references('id')->on('essentials_shifts')->onDelete('cascade');
            

            $table->date('date'); // Date du shift planifié
            $table->text('notes')->nullable();

            $table->timestamps(); // created_at and updated_at
            // Pas de softDeletes pour les entrées de planning

            // Index pour la recherche rapide
            $table->index(['business_location_id', 'date', 'shift_id']);
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_duty_rosters');
    }
};