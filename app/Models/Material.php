<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'type',
        'name'
    ];

    public function UsageRegisters()
    {
        return $this->hasMany('App\Models\UsageRegister');
    }

}
