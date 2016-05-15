<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'status'
    ];

    public function getStartDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = strtotime($value);
    }

    public function bankTransaction()
    {
        return $this->hasMany('App\Models\BankTransaction');
    }
}
