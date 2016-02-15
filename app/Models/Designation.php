<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'name'
    ];

    public function employees()
    {
        return $this->hasMany('App\Models\Employee');
    }

}
