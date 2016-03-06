<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name_en',
        'name_bn',
        'icon',
        'description',
        'ordering'
    ];

    public function component()
    {
        return $this->belongsTo('App\Models\Component');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

}
