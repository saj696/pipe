<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'wage',
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
