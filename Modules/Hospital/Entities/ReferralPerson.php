<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assurez-vous que ce modèle existe

class ReferralPerson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_referral_persons';

    protected $fillable = [
        'business_location_id',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'notes',
    ];

    // Les dates sont gérées automatiquement par le trait SoftDeletes et les timestamps

    /**
     * Get the business location that the referral person belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the payments made to this referral person.
     */
    public function referralPayments()
    {
        return $this->hasMany(ReferralPayment::class, 'referral_person_id');
    }

    // factory() method is provided by HasFactory trait
}