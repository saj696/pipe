<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UsageRegister extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'date',
        'material',
        'color',
        'usage'
    ];

    public function Material()
    {
        return $this->belongsTo('App\Models\Material');
    }

}
