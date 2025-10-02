<?php

namespace Modules\Hopital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QueueItem extends Model
{
    use HasFactory;

    // Possible statuses: waiting, serving, served, cancelled, no-show
    protected $fillable = ['queue_id', 'patient_id', 'visit_id', 'service_id', 'status', 'order', 'called_at', 'served_at'];

    /**
     * Get the queue this item belongs to.
     */
    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    /**
     * Get the patient for this queue item.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the visit associated with this queue item (optional).
     */
    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the service associated with this queue item (optional, e.g., for service-specific queues).
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    protected static function newFactory()
    {
        // return \Modules\Hopital\Database\factories\QueueItemFactory::new();
    }
}