<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PersonalAccount extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'person_type',
        'person_id'
    ];

//    public function UsageRegisters()
//    {
//        return $this->hasMany('App\Models\UsageRegister');
//    }

}
