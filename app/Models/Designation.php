<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public function employees()
    {
        return $this->hasMany('App\Models\Employee');
    }

}
