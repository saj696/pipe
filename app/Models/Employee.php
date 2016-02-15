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

}
