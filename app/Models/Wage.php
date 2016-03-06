<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'extra_hours',
        'bonus',
        'cut'
    ];
}
