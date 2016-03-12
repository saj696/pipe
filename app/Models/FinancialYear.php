<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'year',
        'start_date',
        'end_date'
    ];
}
