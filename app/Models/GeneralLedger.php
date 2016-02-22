<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GeneralLedger extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'account_code',
        'amount',
    ];



}
