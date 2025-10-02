<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToothEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_tooth_entries';

    protected $fillable = [
        'dental_chart_id',
        'tooth_identifier',
        'entry_date',
        'type',
        'condition_name',
        'dental_procedure_id',
        'status',
        'notes',
        'surfaces', // JSON column
        'patient_dental_procedure_id',
    ];

    protected $dates = [
        'entry_date',
    ];

    protected $casts = [
        'surfaces' => 'array', // Cast the 'surfaces' JSON column to an array
    ];

    /**
     * Get the dental chart that owns the entry.
     */
    public function dentalChart()
    {
        return $this->belongsTo(DentalChart::class); // Nécessite la création du modèle DentalChart
    }

    /**
     * Get the dental procedure associated with the entry (if it's a procedure type).
     */
    public function dentalProcedure()
    {
        return $this->belongsTo(DentalProcedure::class); // Nécessite la création du modèle DentalProcedure
    }

    /**
     * Get the patient dental procedure associated with the entry (if it refers to a completed procedure).
     */
    public function patientDentalProcedure()
    {
        return $this->belongsTo(PatientDentalProcedure::class, 'patient_dental_procedure_id'); // Nécessite la création du modèle PatientDentalProcedure
    }

    // factory() method is provided by HasFactory trait
}