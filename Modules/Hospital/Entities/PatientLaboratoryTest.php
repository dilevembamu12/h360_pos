<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assurez-vous que ce namespace est correct
use App\Models\User; // Assurez-vous que ce namespace est correct
use App\Models\TransactionSellLine; // Assurez-vous que ce namespace est correct


class PatientLaboratoryTest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_patient_laboratory_tests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_location_id',
        'patient_id',
        'laboratory_test_id',
        'ordered_by_user_id',
        'performed_by_user_id',
        'order_date',
        'sample_collection_date',
        'report_date',
        'results',
        'notes',
        'opd_visit_id',
        'ipd_admission_id',
        'transaction_sell_line_id',
        'status',
        // 'report_file_id', // Si vous ajoutez ce champ
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'sample_collection_date' => 'date',
        'report_date' => 'date',
        'results' => 'json', // Cast les résultats en JSON
    ];

     /**
     * Get the business location where the test was ordered.
     */
    public function businessLocation()
    {
        // Ajustez le namespace si votre modèle BusinessLocation n'est pas dans App\Models
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }


    /**
     * Get the patient who underwent the test.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the type of laboratory test.
     */
    public function laboratoryTest()
    {
        return $this->belongsTo(LaboratoryTest::class, 'laboratory_test_id');
    }

    /**
     * Get the user who ordered the test.
     */
    public function orderedBy()
    {
        // Ajustez le namespace si votre modèle User n'est pas dans App\Models
        return $this->belongsTo(User::class, 'ordered_by_user_id');
    }

    /**
     * Get the user who performed/validated the test.
     */
    public function performedBy()
    {
         // Ajustez le namespace si votre modèle User n'est pas dans App\Models
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    /**
     * Get the OPD visit related to the test (if any).
     */
    public function opdVisit()
    {
        return $this->belongsTo(OpdVisit::class, 'opd_visit_id');
    }

    /**
     * Get the IPD admission related to the test (if any).
     */
    public function ipdAdmission()
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }

     /**
     * Get the related transaction sell line for billing.
     */
    public function transactionSellLine()
    {
         // Ajustez le namespace si votre modèle TransactionSellLine n'est pas dans App\Models
        return $this->belongsTo(TransactionSellLine::class, 'transaction_sell_line_id');
    }

     /**
     * Get the report file associated with this test (if any).
     */
    /*
    public function reportFile()
    {
        // Assurez-vous que le modèle File existe
        return $this->belongsTo(File::class, 'report_file_id');
    }
    */

     /**
     * Get the files associated with this lab test (using polymorphic relation).
     * Allows attaching multiple files (like raw data, scan of report, etc.)
     */
    public function files()
    {
        // Assurez-vous que le modèle File existe
        return $this->morphMany(File::class, 'related_entity');
    }


    // Factory trait (si vous créez une Factory pour ce modèle)
    // use \Modules\Hospital\Database\Factories\PatientLaboratoryTestFactory;
    // protected static function newFactory()
    // {
    //     return PatientLaboratoryTestFactory::new();
    // }
}