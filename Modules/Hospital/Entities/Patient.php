<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assurez-vous que ce namespace est correct pour votre appli

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_patients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_location_id',
        'record_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'patient_type',
        'blood_group',
        // Ajoutez ici les autres colonnes que vous pourriez ajouter dans la migration
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the business location that the patient belongs to.
     */
    public function businessLocation()
    {
        // Ajustez le namespace si votre modèle BusinessLocation n'est pas dans App\Models
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }

    /**
     * Get the allergies for the patient.
     */
    public function allergies()
    {
        return $this->hasMany(Allergy::class, 'patient_id');
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * Get the IPD admissions for the patient.
     */
    public function ipdAdmissions()
    {
        return $this->hasMany(IpdAdmission::class, 'patient_id');
    }

     /**
     * Get the OPD visits for the patient.
     */
    public function opdVisits()
    {
        return $this->hasMany(OpdVisit::class, 'patient_id');
    }

     /**
     * Get the blood issues for the patient.
     */
    public function bloodIssues()
    {
        return $this->hasMany(BloodIssue::class, 'patient_id');
    }

    /**
     * Get the generated certificates for the patient.
     */
    public function generatedCertificates()
    {
        return $this->hasMany(GeneratedCertificate::class, 'patient_id');
    }

     /**
     * Get the medical history entries for the patient.
     */
    public function medicalHistoryEntries()
    {
        return $this->hasMany(MedicalHistoryEntry::class, 'patient_id');
    }

     /**
     * Get the nurse notes for the patient (typically via IPD).
     */
    public function nurseNotes()
    {
         // Peut être via IpdAdmissions ou directement si les notes sont sur le patient global
         // Si c'est directement sur le patient:
         // return $this->hasMany(NurseNote::class, 'patient_id');
         // Si c'est via admission:
         return $this->hasManyThrough(NurseNote::class, IpdAdmission::class, 'patient_id', 'ipd_admission_id', 'id', 'id');
    }

    /**
     * Get the operative procedures for the patient (typically via IPD).
     */
    public function operativeProcedures()
    {
         // Peut être via IpdAdmissions ou directement si les procédures sont sur le patient global
         // Si c'est directement sur le patient:
         // return $this->hasMany(OperativeProcedure::class, 'patient_id');
         // Si c'est via admission:
         return $this->hasManyThrough(OperativeProcedure::class, IpdAdmission::class, 'patient_id', 'ipd_admission_id', 'id', 'id');
    }

    /**
     * Get the laboratory tests for the patient.
     */
    public function laboratoryTests()
    {
        return $this->hasMany(PatientLaboratoryTest::class, 'patient_id');
    }

    /**
     * Get the radiology tests for the patient.
     */
    public function radiologyTests()
    {
        return $this->hasMany(PatientRadiologyTest::class, 'patient_id');
    }

    /**
     * Get the prescriptions for the patient.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    /**
     * Get the referral payments associated with the patient.
     */
    public function referralPayments()
    {
        return $this->hasMany(ReferralPayment::class, 'patient_id');
    }

    /**
     * Get the vital signs recorded for the patient.
     */
    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class, 'patient_id');
    }

    /**
     * Get the dental chart associated with the patient.
     */
    public function dentalChart()
    {
        return $this->hasOne(DentalChart::class, 'patient_id');
    }

     /**
     * Get the files associated with the patient (using polymorphic relation).
     */
    public function files()
    {
        return $this->morphMany(File::class, 'related_entity');
    }

    /**
     * Get the waiting list entries for the patient (using polymorphic relation).
     */
    public function waitingListEntries()
    {
        return $this->morphMany(WaitingListEntry::class, 'related_entity');
    }

    /**
     * Get the live consultations for the patient (using polymorphic relation).
     */
    public function liveConsultations()
    {
        return $this->morphMany(LiveConsultation::class, 'related_entity');
    }

    // Ajoutez d'autres relations pour les tables qui lient à patient_id
    // comme PatientDentalProcedure, etc. une fois leurs modèles définis.

    // Exemple de méthode pour obtenir le nom complet
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}