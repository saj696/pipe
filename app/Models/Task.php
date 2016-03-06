<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name_en',
        'name_bn',
        'icon',
        'route',
        'description',
        'ordering'
    ];

    public function component()
    {
        return $this->belongsTo('App\Models\Component');
    }

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }

}
