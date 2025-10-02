<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bed extends Model
{
    use HasFactory, SoftDeletes;

    // Définir le nom de la table
    protected $table = 'hospital_beds';

    // Attributs assignables en masse
    protected $fillable = [
        'business_location_id', // Multi-tenancy
        'bed_number',
        'room_number',
        'ward',
        'bed_type',
        'status',
        'notes',
        // 'inventory_item_id', // Si vous activez cette liaison
    ];

    // Définir les relations

    /**
     * Relation avec les attributions de lit associées à ce lit.
     */
    public function bedAllotments()
    {
        return $this->hasMany(BedAllotment::class);
    }

    /**
     * Relation avec l'admission IPD actuellement associée à ce lit (si applicable).
     */
    public function currentIpdAdmission()
    {
        // Note: Cette relation suppose que l'admission IPD a un current_bed_id
        return $this->hasOne(IpdAdmission::class, 'current_bed_id');
    }

     /**
     * Relation avec la Business Location (Hôpital/Succursale).
     */
    public function businessLocation()
    {
        return $this->belongsTo(\App\Models\BusinessLocation::class, 'business_location_id');
    }

    // Si vous activez la liaison avec l'inventaire:
    /*
    public function inventoryItem()
    {
        return $this->belongsTo(\Modules\InventoryManagement\Entities\InventoryItem::class, 'inventory_item_id');
        // Ajustez le namespace si nécessaire
    }
    */

    // factory
    protected static function newFactory()
    {
        return \Modules\Hospital\Database\Factories\BedFactory::new();
    }
}