<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Pas de SoftDeletes selon la migration
use App\Models\User; // Assurez-vous que ce modèle existe
use App\Models\BusinessLocation; // Assurez-vous que ce modèle existe

class StaffProfile extends Model
{
    use HasFactory;

    protected $table = 'hospital_staff_profiles';

    protected $fillable = [
        'user_id',
        'business_location_id',
        'hospital_staff_id',
        'date_of_joining',
        'status',
        'specialist_id',
    ];

    protected $dates = [
        'date_of_joining',
    ];

    /**
     * Get the user associated with the staff profile.
     */
    public function user()
    {
        // Assurez-vous que le modèle User est accessible
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the business location associated with the staff profile.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the specialist associated with the staff profile.
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id');
    }


    // Note: The User model in your core application should have a
    // hasOne(StaffProfile::class) relationship to link back.

    // factory() method is provided by HasFactory trait
}