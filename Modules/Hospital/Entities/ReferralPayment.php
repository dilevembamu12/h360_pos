<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BusinessLocation; // Assurez-vous que ce modèle existe
use App\Models\TransactionPayment; // Assurez-vous que ce modèle existe

class ReferralPayment extends Model
{
    use HasFactory; // Pas de SoftDeletes selon la migration

    protected $table = 'hospital_referral_payments';

    protected $fillable = [
        'business_location_id',
        'referral_person_id',
        'patient_id',
        'amount',
        'payment_date',
        'notes',
        'transaction_payment_id',
    ];

    protected $dates = [
        'payment_date',
    ];

    /**
     * Get the business location that the payment belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the referral person associated with the payment.
     */
    public function referralPerson()
    {
        return $this->belongsTo(ReferralPerson::class, 'referral_person_id');
    }

    /**
     * Get the patient associated with the payment (if any).
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class); // Nécessite la création du modèle Patient
    }

    /**
     * Get the transaction payment associated (if any).
     */
    public function transactionPayment()
    {
        // Assurez-vous que le modèle TransactionPayment est accessible
        return $this->belongsTo(TransactionPayment::class, 'transaction_payment_id');
    }

    // factory() method is provided by HasFactory trait
}