<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProductionRegister extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'date',
        'product',
        'production'
    ];

    public function Product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function getDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date']=strtotime($value);
    }

}
