<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assurez-vous que ce modèle existe
use App\Models\User; // Assurez-vous que ce modèle existe

class WaitingListEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_waiting_list_entries';

    protected $fillable = [
        'business_location_id',
        'patient_id',
        'queue_type',
        'daily_queue_number',
        'entry_time',
        'status',
        'called_time',
        'serving_time',
        'completion_time',
        'cancelled_time',
        'cancelled_by_user_id',
        'managed_by_user_id',
        'notes',
        'related_entity_id',
        'related_entity_type',
    ];

    protected $dates = [
        'entry_time',
        'called_time',
        'serving_time',
        'completion_time',
        'cancelled_time',
    ];

    /**
     * Get the business location that the entry belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the patient associated with the waiting list entry.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class); // Nécessite la création du modèle Patient
    }

    /**
     * Get the user who cancelled the entry (if any).
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id'); // Assurez-vous que le modèle User est accessible
    }

     /**
     * Get the user who managed (called/served) the entry (if any).
     */
    public function managedBy()
    {
        return $this->belongsTo(User::class, 'managed_by_user_id'); // Assurez-vous que le modèle User est accessible
    }


    /**
     * Get the parent model (e.g. Appointment, OpdVisit, IpdAdmission) that the waiting list entry relates to.
     */
    public function relatedEntity()
    {
        return $this->morphTo();
    }

    // Note: The models that can be related entities (Appointment, OpdVisit, IpdAdmission, etc.)
    // will need a morphMany(WaitingListEntry::class, 'related_entity') relationship defined.

    // factory() method is provided by HasFactory trait
}