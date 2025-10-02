<?php

namespace Modules\Hopital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'location']; // Ex: 'Dr. Smith Queue', 'Radiology Queue', 'General Reception Queue'

    /**
     * Get the items in the queue.
     */
    public function items()
    {
        return $this->hasMany(QueueItem::class)->orderBy('order'); // Or orderBy('created_at')
    }

    protected static function newFactory()
    {
        // return \Modules\Hopital\Database\factories\QueueFactory::new();
    }
}