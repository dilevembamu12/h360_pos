<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// No SoftDeletes based on the migration

use App\Models\BusinessLocation; // Assuming BusinessLocation model exists
use App\Models\User; // Assuming User model exists

class GeneratedCertificate extends Model
{
    use HasFactory; // No SoftDeletes based on the migration

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_generated_certificates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_location_id',
        'patient_id',
        'certificate_template_id',
        'issued_by_user_id',
        'issue_date',
        'certificate_number',
        'content',
        'file_path',
        'mime_type',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
    ];


    /**
     * Get the business location that the generated certificate belongs to.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the patient associated with the certificate.
     */
    public function patient()
    {
         // Assuming Patient model exists in the same namespace
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the template used to generate the certificate.
     */
    public function certificateTemplate()
    {
        // Assuming CertificateTemplate model exists in the same namespace
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    /**
     * Get the user who issued the certificate.
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }


    protected static function newFactory()
    {
        // return \Modules\Hospital\Database\Factories\GeneratedCertificateFactory::new();
    }
}