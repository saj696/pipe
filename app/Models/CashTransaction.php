<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    public $timestamps=false;

    protected $fillable = [

    ];

    public function getSendingDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setSendingDateAttribute($value)
    {
        $this->attributes['sending_date']=strtotime($value);
    }

    public function getReceivingDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setReceivingDateAttribute($value)
    {
        $this->attributes['receiving_date']=strtotime($value);
    }
}
