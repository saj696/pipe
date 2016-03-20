<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscardedSales extends Model
{
    public $timestamps = false;

    protected $fillable = [

    ];

    public function DiscardedSalesDetail()
    {
        return $this->hasMany('App\Models\DiscardedSalesDetail');
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
