<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    public $timestamps = false;
    protected $guarded = [
        'id'
    ];
    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
}
