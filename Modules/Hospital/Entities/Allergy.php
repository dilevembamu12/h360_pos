<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Allergy extends Model
{
    use HasFactory, SoftDeletes;

    // Définir le nom de la table si différent de la convention Laravel
    protected $table = 'hospital_allergies';

    // Attributs qui peuvent être assignés en masse
    protected $fillable = [
        'business_location_id', // Pour le multi-tenancy
        'patient_id',
        'name',
        'reaction',
        'notes',
    ];

    // Définir les relations Eloquent

    /**
     * Relation avec le Patient auquel l'allergie est associée.
     */
    public function patient()
    {
        // Utilise la table 'hospital_patients' définie dans le module
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation avec la Business Location (Hôpital/Succursale).
     * Assurez-vous que le modèle BusinessLocation existe dans votre application principale ou un module core.
     */
    public function businessLocation()
    {
        return $this->belongsTo(\App\Models\BusinessLocation::class, 'business_location_id');
        // Si votre modèle BusinessLocation est dans un autre namespace, ajustez le chemin.
    }

    // factory
    protected static function newFactory()
    {
        return \Modules\Hospital\Database\Factories\AllergyFactory::new();
    }
}