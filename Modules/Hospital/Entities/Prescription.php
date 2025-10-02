<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assurez-vous que ce namespace est correct
use App\Models\User; // Assurez-vous que ce namespace est correct
use App\Models\Transaction; // Assurez-vous que ce namespace est correct


class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_prescriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_location_id',
        'patient_id',
        'doctor_user_id',
        'prescription_date',
        'notes',
        'opd_visit_id',
        'ipd_admission_id',
        'transaction_id', // Si l'ordonnance est liée à une transaction globale
        // 'status', // Si vous ajoutez un champ status
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'prescription_date' => 'date',
    ];

     /**
     * Get the business location where the prescription was issued.
     */
    public function businessLocation()
    {
        // Ajustez le namespace si votre modèle BusinessLocation n'est pas dans App\Models
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }


    /**
     * Get the patient associated with the prescription.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the doctor who issued the prescription.
     */
    public function doctor()
    {
        // Ajustez le namespace si votre modèle User n'est pas dans App\Models
        return $this->belongsTo(User::class, 'doctor_user_id');
    }

    /**
     * Get the items on the prescription.
     */
    public function items()
    {
        return $this->hasMany(PrescriptionItem::class, 'prescription_id');
    }

     /**
     * Get the OPD visit related to the prescription (if any).
     */
    public function opdVisit()
    {
        return $this->belongsTo(OpdVisit::class, 'opd_visit_id');
    }

    /**
     * Get the IPD admission related to the prescription (if any).
     */
    public function ipdAdmission()
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }

     /**
     * Get the related transaction for billing (if any).
     */
    public function transaction()
    {
         // Ajustez le namespace si votre modèle Transaction n'est pas dans App\Models
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    // Factory trait
    // use \Modules\Hospital\Database\Factories\PrescriptionFactory;
    // protected static function newFactory()
    // {
    //     return PrescriptionFactory::new();
    // }
}