<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BusinessLocation; // Assuming BusinessLocation model exists in App\Models

class CertificateTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_certificate_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_location_id',
        'name',
        'type',
        'template_html',
        'is_active',
        'notes',
        // 'format', // Uncomment if you added this column in migration
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Cast template_html if specific encoding/decoding is needed, otherwise not required for longText
    ];

    /**
     * Get the business location that the certificate template belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the generated certificates that use this template.
     */
    public function generatedCertificates()
    {
        // Assuming GeneratedCertificate model exists in the same namespace
        return $this->hasMany(GeneratedCertificate::class, 'certificate_template_id');
    }

    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\CertificateTemplateFactory::new();
        // You'll need to create factories in Database/Factories if you use this.
    }
}