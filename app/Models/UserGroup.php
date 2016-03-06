<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name_en',
        'name_bn',
        'ordering'
    ];

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

}
