<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreatmentPlanItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_treatment_plan_items';

    protected $fillable = [
        'treatment_plan_id',
        'dental_procedure_id',
        'tooth_identifier',
        'notes',
        'status',
        'estimated_price',
        'completion_date',
        'patient_dental_procedure_id',
    ];

    protected $dates = [
        'completion_date',
    ];

    /**
     * Get the treatment plan that this item belongs to.
     */
    public function treatmentPlan()
    {
        return $this->belongsTo(TreatmentPlan::class, 'treatment_plan_id');
    }

    /**
     * Get the dental procedure associated with this plan item.
     */
    public function dentalProcedure()
    {
        return $this->belongsTo(DentalProcedure::class, 'dental_procedure_id'); // Nécessite la création du modèle DentalProcedure
    }

    /**
     * Get the completed patient dental procedure associated with this plan item (if completed).
     */
    public function patientDentalProcedure()
    {
        return $this->belongsTo(PatientDentalProcedure::class, 'patient_dental_procedure_id'); // Nécessite la création du modèle PatientDentalProcedure
    }

    // factory() method is provided by HasFactory trait
}