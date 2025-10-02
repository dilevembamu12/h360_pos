<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Pas de SoftDeletes pour les items selon la migration fournie
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product; // Assurez-vous que ce namespace est correct pour votre appli H360
use App\Models\TransactionSellLine; // Assurez-vous que ce namespace est correct


class PrescriptionItem extends Model
{
    use HasFactory; // Pas de SoftDeletes ici

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_prescription_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prescription_id',
        'product_id',
        'dosage',
        'frequency',
        'duration',
        'quantity',
        'quantity_unit',
        'notes',
        'transaction_sell_line_id', // Liaison optionnelle à la ligne de transaction
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2', // Ajustez la précision selon votre besoin
    ];

    /**
     * Get the prescription that the item belongs to.
     */
    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    /**
     * Get the product (medicine) associated with the item.
     */
    public function medicine()
    {
        // Ajustez le namespace si votre modèle Product n'est pas dans App\Models
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the related transaction sell line for billing (if any).
     */
    public function transactionSellLine()
    {
         // Ajustez le namespace si votre modèle TransactionSellLine n'est pas dans App\Models
        return $this->belongsTo(TransactionSellLine::class, 'transaction_sell_line_id');
    }

    // Factory trait
    // use \Modules\Hospital\Database\Factories\PrescriptionItemFactory;
    // protected static function newFactory()
    // {
    //     return PrescriptionItemFactory::new();
    // }
}