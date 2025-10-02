<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmbulanceCall extends Model
{
    use HasFactory, SoftDeletes;

    // Définir le nom de la table
    protected $table = 'hospital_ambulance_calls';

    // Attributs assignables en masse
    protected $fillable = [
        'business_location_id', // Multi-tenancy
        'ambulance_id',
        'patient_id',
        'driver_user_id', // Liaison vers l'utilisateur
        'call_time',
        'pickup_location',
        'drop_location',
        'distance',
        'charges',
        'transaction_sell_line_id', // Liaison vers la ligne de vente (facturation)
        'notes',
    ];

    // Gérer les casts pour les dates/heures si nécessaire
    protected $casts = [
        'call_time' => 'datetime',
    ];


    // Définir les relations

    /**
     * Relation avec l'Ambulance utilisée pour cet appel.
     */
    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }

    /**
     * Relation avec le Patient transporté (si identifié).
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation avec l'utilisateur qui était le chauffeur (peut être différent du driver_name si c'est un champ texte).
     * Assurez-vous que le modèle User existe.
     */
    public function driver()
    {
        return $this->belongsTo(\App\Models\User::class, 'driver_user_id');
    }

    /**
     * Relation avec la ligne de transaction (facturation) si applicable.
     * Assurez-vous que le modèle TransactionSellLine existe.
     */
    public function transactionSellLine()
    {
        return $this->belongsTo(\App\Models\TransactionSellLine::class, 'transaction_sell_line_id');
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
        return \Modules\Hospital\Database\Factories\AmbulanceCallFactory::new();
    }
}