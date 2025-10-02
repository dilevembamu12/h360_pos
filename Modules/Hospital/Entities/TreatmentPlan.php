<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assurez-vous que ce modèle existe
use App\Models\User; // Assurez-vous que ce modèle existe
use App\Models\Transaction; // Assurez-vous que ce modèle existe pour les devis

class TreatmentPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_treatment_plans';

    protected $fillable = [
        'business_location_id',
        'patient_id',
        'dentist_user_id',
        'plan_date',
        'status',
        'notes',
        'total_amount',
        'quotation_transaction_id',
    ];

    protected $dates = [
        'plan_date',
    ];

    /**
     * Get the business location that owns the treatment plan.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the patient associated with the treatment plan.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class); // Nécessite la création du modèle Patient
    }

    /**
     * Get the dentist who created the treatment plan.
     */
    public function dentist()
    {
        return $this->belongsTo(User::class, 'dentist_user_id'); // Assurez-vous que le modèle User est accessible
    }

    /**
     * Get the items/steps within the treatment plan.
     */
    public function items()
    {
        return $this->hasMany(TreatmentPlanItem::class, 'treatment_plan_id');
    }

    /**
     * Get the related quotation transaction (if any).
     */
    public function quotationTransaction()
    {
        // Assurez-vous que le modèle Transaction est accessible
        return $this->belongsTo(Transaction::class, 'quotation_transaction_id');
    }


    // factory() method is provided by HasFactory trait
}