<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ambulance extends Model
{
    use HasFactory, SoftDeletes;

    // Définir le nom de la table
    protected $table = 'hospital_ambulances';

    // Attributs assignables en masse
    protected $fillable = [
        'business_location_id', // Multi-tenancy
        'vehicle_number',
        'vehicle_model',
        'driver_name',
        'driver_contact',
        'status',
        'notes',
    ];

    // Définir les relations

    /**
     * Relation avec les appels d'ambulance associés à cette ambulance.
     */
    public function ambulanceCalls()
    {
        return $this->hasMany(AmbulanceCall::class);
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
        return \Modules\Hospital\Database\Factories\AmbulanceFactory::new();
    }
}