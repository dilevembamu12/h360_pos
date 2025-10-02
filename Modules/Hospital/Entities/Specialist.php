<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assurez-vous que ce modèle existe

class Specialist extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_specialists';

    protected $fillable = [
        'business_location_id',
        'name',
        'description',
    ];

    // Les dates sont gérées automatiquement par le trait SoftDeletes et les timestamps

    /**
     * Get the business location that the specialist belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the staff profiles with this specialization.
     */
    public function staffProfiles()
    {
        return $this->hasMany(StaffProfile::class, 'specialist_id');
    }

    // factory() method is provided by HasFactory trait
}