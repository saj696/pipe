<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TransactionRecorder extends Model
{
    public $timestamps=false;

    protected $fillable = [];

//    public function UsageRegisters()
//    {
//        return $this->hasMany('App\Models\UsageRegister');
//    }

}
