<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobilemoneyPayLine extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    public function transaction()
    {
        return $this->belongsTo(\App\Currency::class, 'transaction_id');
    }

    public function currency()
    {
        return $this->belongsTo(\App\Currency::class, 'currency_id');
    }



    public function status()
    {
        $statuses = ['received', 'pending', 'canceled', 'draft', 'final', 'failed'];
    }

    
}
