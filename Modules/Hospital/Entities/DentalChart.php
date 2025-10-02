<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assuming BusinessLocation model exists
use App\Models\User; // Assuming User model exists

class DentalChart extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_dental_charts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_location_id',
        'patient_id',
        'chart_type',
        'tooth_numbering_system',
        'last_updated_by_user_id',
        'last_updated_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the business location that the dental chart belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the patient that owns the dental chart.
     */
    public function patient()
    {
        // Assuming Patient model exists in the same namespace
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the user who last updated the dental chart.
     */
    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by_user_id');
    }

    /**
     * Get the tooth entries for the dental chart.
     */
    public function toothEntries()
    {
        // Assuming ToothEntry model exists in the same namespace
        return $this->hasMany(ToothEntry::class, 'dental_chart_id');
    }


    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\DentalChartFactory::new();
    }
}