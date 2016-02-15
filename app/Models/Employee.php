<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'present_address',
        'permanent_address',
        'dob'
    ];

    public function designation()
    {
        return $this->belongsTo('App\Models\Designation');
    }

    public function getDobAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setDobAttribute($value)
    {
        $this->attributes['dob']=strtotime($value);
    }

    public function getJoiningDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setJoiningDateAttribute($value)
    {
        $this->attributes['joining_date']=strtotime($value);
    }
}
