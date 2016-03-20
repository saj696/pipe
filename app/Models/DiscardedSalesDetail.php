<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscardedSalesDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [

    ];

    public function material()
    {
        return $this->hasOne('App\Models\Material');
    }

    public function DiscardedSales()
    {
        return $this->hasOne('App\Models\DiscardedSales');
    }
}
