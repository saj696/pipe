<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table='customer';
    public $timestamps=false;

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
