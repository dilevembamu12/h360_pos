<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User; // Assurez-vous que ce namespace est correct
use App\Models\TransactionSellLine; // Assurez-vous que ce namespace est correct

// *** ATTENTION ***
// La migration 2025_04_25_234947_create_hospital_patient_dental_procedures_table.php est actuellement vide.
// Le contenu de ce modèle est basé sur une structure supposée pour une procédure dentaire effectuée sur un patient.
// Il faudra Mettre à jour la migration et potentiellement ce modèle en fonction de la structure réelle.

class PatientDentalProcedure extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_patient_dental_procedures';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Assurez-vous que ces colonnes existent dans la migration complétée
        'business_location_id', // Probablement besoin d'un lien multi-tenant ici aussi
        'patient_id',
        'dental_procedure_id',
        'tooth_identifier', // Ou 'teeth' si c'est un JSON d'identifiants
        'procedure_date',
        'status', // ex: 'completed', 'failed', 'cancelled'
        'notes',
        'recorded_by_user_id',
        'transaction_sell_line_id', // Lien à la facturation
        // ... autres champs si nécessaire
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'procedure_date' => 'date',
        // 'tooth_identifier' => 'json', // Si c'est un JSON pour plusieurs dents/surfaces
    ];

     /**
     * Get the business location that the procedure was performed at.
     */
    public function businessLocation()
    {
        // Ajustez le namespace si votre modèle BusinessLocation n'est pas dans App\Models
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }


    /**
     * Get the patient that the procedure was performed on.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the type of dental procedure performed.
     */
    public function dentalProcedure()
    {
        return $this->belongsTo(DentalProcedure::class, 'dental_procedure_id');
    }

    /**
     * Get the user who recorded the procedure.
     */
    public function recorder()
    {
        // Ajustez le namespace si votre modèle User n'est pas dans App\Models
        return $this->belongsTo(User::class, 'recorded_by_user_id');
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
     * Get the tooth entries related to this procedure.
     */
    public function toothEntries()
    {
        // Vérifiez si la colonne patient_dental_procedure_id existe dans hospital_tooth_entries
        return $this->hasMany(ToothEntry::class, 'patient_dental_procedure_id');
    }

     /**
     * Get the treatment plan items related to this procedure.
     */
    public function treatmentPlanItems()
    {
        // Vérifiez si la colonne patient_dental_procedure_id existe dans hospital_treatment_plan_items
        return $this->hasMany(TreatmentPlanItem::class, 'patient_dental_procedure_id');
    }


    // Ajoutez d'autres relations si nécessaire (ex: MorphTo pour lier à OPD/IPD visit)
}