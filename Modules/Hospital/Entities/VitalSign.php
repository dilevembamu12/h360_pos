<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Pas de SoftDeletes selon la migration
use App\Models\BusinessLocation; // Assurez-vous que ce modèle existe
use App\Models\User; // Assurez-vous que ce modèle existe

class VitalSign extends Model
{
    use HasFactory;

    protected $table = 'hospital_vital_signs';

    protected $fillable = [
        'business_location_id',
        'patient_id',
        'ipd_admission_id',
        'recorded_by_user_id',
        'recorded_at',
        'temperature',
        'pulse_rate',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'respiration_rate',
        'oxygen_saturation',
        'height',
        'weight',
        'bmi',
        'notes',
    ];

    protected $dates = [
        'recorded_at', // Carbon instance for recorded_at
        'created_at', // Carbon instance for created_at (Eloquent default)
        'updated_at', // Carbon instance for updated_at (Eloquent default)
    ];

     // Pas de $casts ici car les colonnes sont numériques

    /**
     * Get the business location that the vital sign belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the patient associated with the vital sign.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class); // Nécessite la création du modèle Patient
    }

    /**
     * Get the IPD admission associated with the vital sign (if any).
     */
    public function ipdAdmission()
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id'); // Nécessite la création du modèle IpdAdmission
    }

    /**
     * Get the user who recorded the vital sign.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id'); // Assurez-vous que le modèle User est accessible
    }

    // factory() method is provided by HasFactory trait
}