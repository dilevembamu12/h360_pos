<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Pas de SoftDeletes selon la migration

class Appointment extends Model
{
    use HasFactory;

    // Définir le nom de la table
    protected $table = 'hospital_appointments';

    // Attributs assignables en masse
    protected $fillable = [
        'business_location_id', // Multi-tenancy
        'patient_id',
        'doctor_user_id', // Liaison vers l'utilisateur médecin
        'appointment_date',
        'appointment_time',
        'status',
        'appointment_type',
        'live_consultation_id', // Liaison vers la consultation en direct
        'notes',
        'amount',
        'payment_status',
        'transaction_id', // Liaison vers la transaction (facture)
    ];

    // Gérer les casts pour les dates/heures
    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'string', // L'heure est stockée comme string 'HH:MM:SS'
    ];

    // Définir les relations

    /**
     * Relation avec le Patient qui a pris le rendez-vous.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation avec l'utilisateur qui est le médecin consulté.
     * Assurez-vous que le modèle User existe.
     */
    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'doctor_user_id');
    }

    /**
     * Relation avec la consultation en direct associée à ce rendez-vous.
     */
    public function liveConsultation()
    {
        return $this->belongsTo(LiveConsultation::class, 'live_consultation_id');
    }

    /**
     * Relation avec la transaction (facture) associée à ce rendez-vous.
     * Assurez-vous que le modèle Transaction existe.
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Models\Transaction::class, 'transaction_id');
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
        return \Modules\Hospital\Database\Factories\AppointmentFactory::new();
    }
}