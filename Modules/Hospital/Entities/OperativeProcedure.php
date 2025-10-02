// File: Modules/Hospital/Entities/OperativeProcedure.php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
// Assurez-vous que App\Models\User, App\Models\TransactionSellLine et App\Models\BusinessLocation sont les bons namespaces dans votre application H360🛒POS
use App\Models\User; // Assuming User model is in App\Models
use App\Models\TransactionSellLine; // Assuming TransactionSellLine model is in App\Models
use App\Models\BusinessLocation; // Assuming BusinessLocation model is in App\Models


class OperativeProcedure extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_operative_procedures';

    protected $fillable = [
        'business_location_id',
        'patient_id',
        'ipd_admission_id',
        'procedure_date',
        'procedure_time',
        'procedure_name',
        'description',
        'surgeon_user_id',
        'anesthetist_user_id',
        'assistant_surgeons_users_ids',
        'nurses_users_ids',
        'notes',
        'complications',
        'status',
        'transaction_sell_line_id',
    ];

    protected $casts = [
        'procedure_date' => 'date',
        // 'procedure_time' is a time string
        'assistant_surgeons_users_ids' => 'array', // Cast JSON field to array
        'nurses_users_ids' => 'array',           // Cast JSON field to array
    ];

     /**
     * Get the hospital (business location) this procedure belongs to.
     */
    public function businessLocation()
    {
         // Assurez-vous que 'App\Models\BusinessLocation' est le modèle correct pour votre application
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }


    /**
     * Get the patient who underwent the procedure.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the IPD admission associated with the procedure.
     */
    public function ipdAdmission()
    {
        // Nécessite le modèle IpdAdmission
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }

    /**
     * Get the surgeon who performed the procedure.
     */
    public function surgeon()
    {
         // Assurez-vous que 'App\Models\User' est le modèle correct pour votre application
        return $this->belongsTo(User::class, 'surgeon_user_id');
    }

    /**
     * Get the anesthetist involved in the procedure.
     */
    public function anesthetist()
    {
         // Assurez-vous que 'App\Models\User' est le modèle correct pour votre application
        return $this->belongsTo(User::class, 'anesthetist_user_id');
    }

     /**
     * Get the transaction sell line (billing item) for this procedure.
     */
    public function transactionSellLine()
    {
         // Assurez-vous que 'App\Models\TransactionSellLine' est le modèle correct pour votre application
        return $this->belongsTo(TransactionSellLine::class, 'transaction_sell_line_id');
    }


    // Accessor to get assistant surgeons
    // public function getAssistantSurgeonsAttribute()
    // {
    //     if (empty($this->assistant_surgeons_users_ids)) {
    //         return collect();
    //     }
    //     // Assurez-vous que 'App\Models\User' est le modèle correct
    //     return User::whereIn('id', $this->assistant_surgeons_users_ids)->get();
    // }

    // Accessor to get nurses involved
    // public function getNursesAttribute()
    // {
    //     if (empty($this->nurses_users_ids)) {
    //         return collect();
    //     }
    //      // Assurez-vous que 'App\Models\User' est le modèle correct
    //     return User::whereIn('id', $this->nurses_users_ids)->get();
    // }


    // Si vous utilisez des factories
    // protected static function newFactory()
    // {
    //     return \Modules\Hospital\Database\Factories\OperativeProcedureFactory::new();
    // }
}