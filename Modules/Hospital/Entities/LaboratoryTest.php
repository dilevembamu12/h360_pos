<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assuming BusinessLocation model exists

class LaboratoryTest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_laboratory_tests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_location_id', // Linked to BusinessLocation as per your migration correction
        'name',
        'short_name',
        'description',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2', // Cast price to decimal with 2 places
    ];

    /**
     * Get the business location that the laboratory test belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }


    /**
     * Get the patient laboratory tests records for this test.
     */
    public function patientTests()
    {
        // Assuming PatientLaboratoryTest model exists in the same namespace
        return $this->hasMany(PatientLaboratoryTest::class, 'laboratory_test_id');
    }


    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\LaboratoryTestFactory::new();
    }
}