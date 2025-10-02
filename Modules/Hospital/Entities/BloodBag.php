<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodBag extends Model
{
    use HasFactory, SoftDeletes;

    // Définir le nom de la table
    protected $table = 'hospital_blood_bags';

    // Attributs assignables en masse
    protected $fillable = [
        'business_location_id', // Multi-tenancy
        'donor_id', // Liaison vers le donneur
        'bag_number',
        'blood_group',
        'component',
        'collection_date',
        'expiry_date',
        'status',
        'notes',
    ];

    // Gérer les casts pour les dates
    protected $casts = [
        'collection_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Définir les relations

    /**
     * Relation avec le Donneur associé à cette poche de sang.
     */
    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    /**
     * Relation avec les délivrances de sang (BloodIssues) de cette poche.
     */
    public function bloodIssues()
    {
        return $this->hasMany(BloodIssue::class);
    }

     /**
     * Relation avec la Business Location (Hôpital/Succursale).
     */
    public function businessLocation()
    {
        return $this->belongsTo(\App\Models\BusinessLocation::class, 'business_location_id');
    }

    // factory
    protected static function newFactory()
    {
        return \Modules\Hospital\Database\Factories\BloodBagFactory::new();
    }
}