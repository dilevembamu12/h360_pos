<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// No SoftDeletes based on the migration

use App\Models\BusinessLocation; // Assuming BusinessLocation model exists
use App\Models\User; // Assuming User model exists
use Modules\Essentials\Entities\Shift; // Assuming Shift model exists in Essentials module

class DutyRoster extends Model
{
    use HasFactory; // No SoftDeletes based on the migration

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_duty_rosters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_location_id',
        'user_id',
        'shift_id',
        'date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the business location that the duty roster entry belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the user (staff member) assigned to the shift.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shift details for the duty roster entry.
     */
    public function shift()
    {
        // Assuming Shift model exists in the Essentials module
        return $this->belongsTo(Shift::class, 'shift_id');
    }


    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\DutyRosterFactory::new();
    }
}