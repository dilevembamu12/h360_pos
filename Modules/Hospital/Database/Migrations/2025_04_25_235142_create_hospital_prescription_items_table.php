<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalPrescriptionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_prescription_items', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Liaison vers l'ordonnance (Prescription) - Mandataire
            $table->foreignId('prescription_id')->constrained('hospital_prescriptions')->onDelete('cascade'); // Si l'ordonnance est supprimée, ses items aussi

            // Liaison vers le produit (médicament) - Mandataire
            // Assurez-vous que le type de produit 'medicine' existe ou est géré
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');


            $table->string('dosage')->nullable(); // Dosage (ex: '500mg')
            $table->string('frequency')->nullable(); // Fréquence (ex: '2x/jour')
            $table->string('duration')->nullable(); // Durée (ex: '7 jours')
            $table->decimal('quantity', 10, 2)->default(0); // Quantité prescrite (ex: 14)
            $table->string('quantity_unit')->nullable(); // Unité de quantité (ex: 'comprimés', 'ml')
            $table->text('notes')->nullable(); // Instructions spécifiques (ex: 'prendre avec de la nourriture')

             // Liaison optionnelle vers la ligne de transaction (facture) si cet item est facturé individuellement
            // $table->integer('transaction_sell_line_id')->nullable()->unsigned();
            $table->integer('transaction_sell_line_id')->nullable()->unsigned();
            $table->foreign('transaction_sell_line_id')->nullable()->references('id')->on('transaction_sell_lines')->onDelete('set null');
                        

            $table->timestamps();
            // Pas de softDeletes pour les lignes d'items généralement

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_prescription_items');
    }
}