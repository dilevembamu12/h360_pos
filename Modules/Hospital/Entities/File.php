<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assuming BusinessLocation model exists
use App\Models\User; // Assuming User model exists

class File extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_location_id',
        'related_entity_id',
        'related_entity_type',
        'user_id', // Uploader
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'description',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'int',
    ];


    /**
     * Get the business location that the file belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the user who uploaded the file.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent model that the file is related to (polymorphic relation).
     */
    public function relatedEntity()
    {
        return $this->morphTo();
    }


    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\FileFactory::new();
    }
}