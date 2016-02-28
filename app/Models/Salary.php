<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'extra_hours',
        'bonus',
        'cut'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
