<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $timestamps = false;
    protected $table = 'customer';
    protected $fillable = [
        'name',
        'mobile',
        'address',
        'type',
        'business_name',
        'business_address',
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
