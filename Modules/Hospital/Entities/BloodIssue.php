<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodIssue extends Model
{
    use HasFactory, SoftDeletes;

    // Définir le nom de la table
    protected $table = 'hospital_blood_issues';

    // Attributs assignables en masse
    protected $fillable = [
        'business_location_id', // Multi-tenancy
        'patient_id', // Liaison vers le receveur (Patient)
        'blood_bag_id', // Liaison vers la poche de sang
        'issued_by_user_id', // Liaison vers l'utilisateur qui a délivré
        'issue_date',
        'transaction_sell_line_id', // Liaison vers la ligne de transaction (facturation)
        'notes',
    ];

    // Gérer les casts pour les dates/heures
    protected $casts = [
        'issue_date' => 'datetime',
    ];

    // Définir les relations

    /**
     * Relation avec le Patient qui a reçu le sang.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation avec la Poche de Sang qui a été délivrée.
     */
    public function bloodBag()
    {
        return $this->belongsTo(BloodBag::class);
    }

    /**
     * Relation avec l'utilisateur qui a délivré le sang.
     * Assurez-vous que le modèle User existe.
     */
    public function issuedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'issued_by_user_id');
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
        return \Modules\Hospital\Database\Factories\BloodIssueFactory::new();
    }
}