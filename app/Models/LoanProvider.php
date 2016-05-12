<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanProvider extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'mobile',
        'address',
        'company_name',
        'company_address',
        'status'
    ];

    /* public function component()
     {
         return $this->belongsTo('App\Models\Component');
     }

     public function tasks()
     {
         return $this->hasMany('App\Models\Task');
     }*/
}
