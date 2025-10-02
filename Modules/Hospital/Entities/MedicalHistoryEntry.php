// File: Modules/Hospital/Entities/MedicalHistoryEntry.php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
// Assurez-vous que App\Models\User et App\Models\BusinessLocation sont les bons namespaces dans votre application H360🛒POS
use App\Models\User; // Assuming User model is in App\Models
use App\Models\BusinessLocation; // Assuming BusinessLocation model is in App\Models

class MedicalHistoryEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_medical_history_entries';

    protected $fillable = [
        'business_location_id',
        'patient_id',
        'entry_date',
        'entry_type',
        'summary',
        'details',
        'recorded_by_user_id',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    /**
     * Get the hospital (business location) this entry belongs to.
     */
    public function businessLocation()
    {
        // Assurez-vous que 'App\Models\BusinessLocation' est le modèle correct pour votre application
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }

    /**
     * Get the patient that owns the medical history entry.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the user who recorded the medical history entry.
     */
    public function recordedBy()
    {
         // Assurez-vous que 'App\Models\User' est le modèle correct pour votre application
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    // Si vous utilisez des factories pour le module
    // protected static function newFactory()
    // {
    //     return \Modules\Hospital\Database\Factories\MedicalHistoryEntryFactory::new();
    // }
}