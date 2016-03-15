<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscardedSales extends Model
{
    public $timestamps = false;

    protected $fillable = [

    ];

    public function material()
    {
        return $this->hasMany('App\Models\Material');
    }

}
