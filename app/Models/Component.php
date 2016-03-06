<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name_en',
        'name_bn',
        'icon',
        'description',
        'ordering'
    ];

    public function modules()
    {
        return $this->hasMany('App\Models\Module');
    }

}
