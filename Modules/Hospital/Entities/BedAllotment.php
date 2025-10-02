<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BedAllotment extends Model
{
    use HasFactory, SoftDeletes;

    // Définir le nom de la table
    protected $table = 'hospital_bed_allotments';

    // Attributs assignables en masse
    protected $fillable = [
        'business_location_id', // Multi-tenancy
        'patient_id',
        'bed_id',
        'ipd_admission_id', // Liaison vers l'admission IPD
        'allotment_time',
        'discharge_time',
        'notes',
        // 'price_per_day', // Si vous ajoutez ce champ dans la migration
    ];

    // Gérer les casts pour les dates/heures
    protected $casts = [
        'allotment_time' => 'datetime',
        'discharge_time' => 'datetime',
    ];

    // Définir les relations

    /**
     * Relation avec le Patient attribué à ce lit.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation avec le Lit attribué.
     */
    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * Relation avec l'Admission IPD associée à cette attribution.
     */
    public function ipdAdmission()
    {
        return $this->belongsTo(IpdAdmission::class);
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
        return \Modules\Hospital\Database\Factories\BedAllotmentFactory::new();
    }
}