<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assurez-vous que ce modèle existe

class RadiologyTest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hospital_radiology_tests';

    protected $fillable = [
        'business_location_id',
        'name',
        'short_name',
        'description',
        'price',
    ];

    // Les dates sont gérées automatiquement par le trait SoftDeletes et les timestamps

    /**
     * Get the business location that owns the test.
     */
    public function businessLocation()
    {
        // Assurez-vous que le modèle BusinessLocation est accessible via ce namespace
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the patient radiology tests for this test.
     */
    public function patientRadiologyTests()
    {
        return $this->hasMany(PatientRadiologyTest::class); // Nécessite la création du modèle PatientRadiologyTest
    }

    // factory() method is provided by HasFactory trait
}