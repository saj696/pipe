<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
