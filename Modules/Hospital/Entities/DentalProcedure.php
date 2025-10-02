<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
// use App\Models\BusinessLocation; // Uncomment if linking procedures to Business Location

class DentalProcedure extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_dental_procedures';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'business_location_id', // Uncomment if linking procedures to Business Location
        'code',
        'name',
        'description',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2', // Cast price to decimal with 2 places
    ];

    // Uncomment if linking procedures to Business Location
    /*
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }
    */

    /**
     * Get the tooth entries that reference this procedure.
     */
    public function toothEntries()
    {
         // Assuming ToothEntry model exists in the same namespace
        return $this->hasMany(ToothEntry::class, 'dental_procedure_id');
    }

     /**
     * Get the treatment plan items that reference this procedure.
     */
    public function treatmentPlanItems()
    {
         // Assuming TreatmentPlanItem model exists in the same namespace
        return $this->hasMany(TreatmentPlanItem::class, 'dental_procedure_id');
    }

     /**
     * Get the patient dental procedures that reference this procedure.
     */
    public function patientProcedures()
    {
         // Assuming PatientDentalProcedure model exists in the same namespace
        return $this->hasMany(PatientDentalProcedure::class, 'dental_procedure_id');
    }


    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\DentalProcedureFactory::new();
    }
}