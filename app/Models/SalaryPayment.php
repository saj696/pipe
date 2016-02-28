<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'amount'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
