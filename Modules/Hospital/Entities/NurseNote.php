// File: Modules/Hospital/Entities/NurseNote.php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
// Assurez-vous que App\Models\User et App\Models\BusinessLocation sont les bons namespaces dans votre application H360🛒POS
use App\Models\User; // Assuming User model is in App\Models
use App\Models\BusinessLocation; // Assuming BusinessLocation model is in App\Models


class NurseNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_nurse_notes';

    protected $fillable = [
        'business_location_id',
        'patient_id',
        'ipd_admission_id',
        'recorded_by_user_id',
        'note_date',
        'note_time',
        'notes',
    ];

    protected $casts = [
        'note_date' => 'date',
        // 'note_time' is a time string, no specific cast needed unless you handle it
    ];

     /**
     * Get the hospital (business location) this note belongs to.
     */
    public function businessLocation()
    {
         // Assurez-vous que 'App\Models\BusinessLocation' est le modèle correct pour votre application
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }


    /**
     * Get the patient that the nurse note belongs to.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the IPD admission that the nurse note belongs to.
     */
    public function ipdAdmission()
    {
        // Nécessite le modèle IpdAdmission
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }

    /**
     * Get the user (nurse/staff) who recorded the note.
     */
    public function recordedBy()
    {
         // Assurez-vous que 'App\Models\User' est le modèle correct pour votre application
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    // Si vous utilisez des factories
    // protected static function newFactory()
    // {
    //     return \Modules\Hospital\Database\Factories\NurseNoteFactory::new();
    // }
}