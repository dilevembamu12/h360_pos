<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assuming BusinessLocation model exists

class Donor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_donors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_location_id',
        'donor_id', // Internal donor ID
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'blood_group',
        'last_donation_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'last_donation_date' => 'date',
    ];

    /**
     * Get the business location that the donor belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the blood bags collected from this donor.
     */
    public function bloodBags()
    {
        // Assuming BloodBag model exists in the same namespace
        return $this->hasMany(BloodBag::class, 'donor_id');
    }


    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\DonorFactory::new();
    }
}