<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [];

    public function getTransactionDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setTransactionDateAttribute($value)
    {
        $this->attributes['transaction_date'] = strtotime($value);
    }

     public function bank()
     {
         return $this->belongsTo('App\Models\Bank');
     }
}
