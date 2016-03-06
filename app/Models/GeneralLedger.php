<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralLedger extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'account_code',
        'amount',
    ];


}
