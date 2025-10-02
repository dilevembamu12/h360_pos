// File: Modules/Hospital/Entities/OpdVisit.php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
// Assurez-vous que App\Models\User, App\Models\Transaction et App\Models\BusinessLocation sont les bons namespaces dans votre application H360🛒POS
use App\Models\User; // Assuming User model is in App\Models
use App\Models\Transaction; // Assuming Transaction model is in App\Models
use App\Models\BusinessLocation; // Assuming BusinessLocation model is in App\Models


class OpdVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_opd_visits';

    protected $fillable = [
        'business_location_id',
        'patient_id',
        'opd_number',
        'appointment_id',
        'visit_date',
        'doctor_user_id',
        'status',
        'symptoms',
        'diagnosis',
        'treatment',
        'notes',
        'discharge_status',
        'transaction_id',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

     /**
     * Get the hospital (business location) this visit belongs to.
     */
    public function businessLocation()
    {
         // Assurez-vous que 'App\Models\BusinessLocation' est le modèle correct pour votre application
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }


    /**
     * Get the patient that the OPD visit belongs to.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the appointment associated with the OPD visit.
     */
    public function appointment()
    {
        // Nécessite le modèle Appointment
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    /**
     * Get the doctor who handled the OPD visit.
     */
    public function doctor()
    {
         // Assurez-vous que 'App\Models\User' est le modèle correct pour votre application
        return $this->belongsTo(User::class, 'doctor_user_id');
    }

    /**
     * Get the transaction (invoice) associated with the OPD visit.
     */
    public function transaction()
    {
         // Assurez-vous que 'App\Models\Transaction' est le modèle correct pour votre application
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    // Vous pouvez ajouter d'autres relations ici pour les éléments liés à une visite OPD:
    // - Examens de laboratoire: hasMany(PatientLaboratoryTest::class)
    // - Examens de radiologie: hasMany(PatientRadiologyTest::class)
    // - Ordonnances: hasMany(Prescription::class)
    // - Fichiers: morphMany(File::class, 'related_entity')
    // - Consultations en direct: morphOne(LiveConsultation::class, 'related_entity')

    // Si vous utilisez des factories
    // protected static function newFactory()
    // {
    //     return \Modules\Hospital\Database\Factories\OpdVisitFactory::new();
    // }
}