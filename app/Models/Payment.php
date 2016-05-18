<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [];

    public function getDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = strtotime($value);
    }

}
