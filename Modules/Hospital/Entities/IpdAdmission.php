<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assuming BusinessLocation model exists
use App\Models\User; // Assuming User model exists
use App\Models\Transaction; // Assuming Transaction model exists (main POS transaction)

class IpdAdmission extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_ipd_admissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_location_id',
        'patient_id',
        'ipd_number',
        'admission_date',
        'discharge_date',
        'current_bed_id',
        'consultant_doctor_user_id',
        'case_details',
        'symptoms',
        'diagnosis',
        'notes',
        'discharge_summary',
        'status',
        'transaction_id', // Link to the main IPD invoice/transaction
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
    ];

    /**
     * Get the business location that the IPD admission belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the patient associated with the IPD admission.
     */
    public function patient()
    {
        // Assuming Patient model exists in the same namespace
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the current bed assigned to the patient during this admission.
     */
    public function currentBed()
    {
        // Assuming Bed model exists in the same namespace
        return $this->belongsTo(Bed::class, 'current_bed_id');
    }

    /**
     * Get the consultant doctor for this admission.
     */
    public function consultantDoctor()
    {
        return $this->belongsTo(User::class, 'consultant_doctor_user_id');
    }

    /**
     * Get the main transaction/invoice for this IPD admission.
     */
    public function transaction()
    {
        // Assuming Transaction model exists in the main app
        return $this->belongsTo(Transaction::class);
    }

    // --- Relationships to related entries during this admission ---

    /**
     * Get the history of bed allotments for this admission.
     */
    public function bedAllotments()
    {
        // Assuming BedAllotment model exists in the same namespace
        return $this->hasMany(BedAllotment::class, 'ipd_admission_id');
    }

    /**
     * Get the nurse notes for this admission.
     */
    public function nurseNotes()
    {
        // Assuming NurseNote model exists in the same namespace
        return $this->hasMany(NurseNote::class, 'ipd_admission_id');
    }

     /**
     * Get the vital signs recorded during this admission.
     */
    public function vitalSigns()
    {
        // Assuming VitalSign model exists in the same namespace
        return $this->hasMany(VitalSign::class, 'ipd_admission_id');
    }

    /**
     * Get the operative procedures performed during this admission.
     */
    public function operativeProcedures()
    {
        // Assuming OperativeProcedure model exists in the same namespace
        return $this->hasMany(OperativeProcedure::class, 'ipd_admission_id');
    }

     /**
     * Get the laboratory tests ordered during this admission.
     */
    public function patientLaboratoryTests()
    {
        // Assuming PatientLaboratoryTest model exists in the same namespace
        return $this->hasMany(PatientLaboratoryTest::class, 'ipd_admission_id');
    }

    /**
     * Get the radiology tests ordered during this admission.
     */
     public function patientRadiologyTests()
    {
        // Assuming PatientRadiologyTest model exists in the same namespace
        return $this->hasMany(PatientRadiologyTest::class, 'ipd_admission_id');
    }

    /**
     * Get the prescriptions issued during this admission.
     */
    public function prescriptions()
    {
        // Assuming Prescription model exists in the same namespace
        return $this->hasMany(Prescription::class, 'ipd_admission_id');
    }

    /**
     * Get the live consultations related to this admission (if using polymorphic relation).
     */
    public function liveConsultations()
    {
         // Assuming LiveConsultation model exists in the same namespace
        return $this->morphMany(LiveConsultation::class, 'related_entity');
    }


    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\IpdAdmissionFactory::new();
    }
}