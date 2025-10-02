<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assuming BusinessLocation model exists
use App\Models\User; // Assuming User model exists

class LiveConsultation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_live_consultations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_location_id',
        'patient_id',
        'doctor_user_id',
        'consultation_date',
        'start_time',
        'end_time',
        'platform',
        'platform_meeting_id',
        'platform_meeting_url',
        'status',
        'notes',
        'related_entity_id', // For polymorphic relation
        'related_entity_type', // For polymorphic relation
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'consultation_date' => 'datetime',
        // start_time and end_time are 'time' in DB, can be string in model
    ];

    /**
     * Get the business location that the live consultation belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the patient associated with the live consultation.
     */
    public function patient()
    {
        // Assuming Patient model exists in the same namespace
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the doctor who conducted the live consultation.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_user_id');
    }

     /**
     * Get the parent model that the live consultation is related to (polymorphic relation).
     * This could be an Appointment, an OpdVisit, or an IpdAdmission based on your prompt.
     */
    public function relatedEntity()
    {
        return $this->morphTo();
    }

    /**
     * Get the Appointment associated with this live consultation (if any, based on appointment->live_consultation_id).
     * Note: This is a potential inverse relationship. An Appointment belongsTo LiveConsultation.
     * A LiveConsultation *might* have one Appointment linked *via* the appointment table.
     */
    public function appointment()
    {
         // Assuming Appointment model exists in the same namespace
        return $this->hasOne(Appointment::class, 'live_consultation_id');
    }


    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\LiveConsultationFactory::new();
    }
}