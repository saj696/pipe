<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralJournal extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'account_code',
        'amount',
    ];
}
