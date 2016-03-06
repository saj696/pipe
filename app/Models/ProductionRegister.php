<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionRegister extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date',
        'product',
        'production'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function getDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = strtotime($value);
    }

}
