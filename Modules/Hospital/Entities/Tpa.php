<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assurez-vous que ce modèle existe

class Tpa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_tpas';

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
     * Get the business location that the TPA belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    // You might want to add relationships here, e.g., hasMany('PatientInsurance', 'tpa_id')
    // if you have a table linking patients/invoices to TPAs.

    // factory() method is provided by HasFactory trait
}