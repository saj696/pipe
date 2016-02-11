<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'type',
        'name'
    ];

//    public function users()
//    {
//        return $this->hasMany('App\Models\User');
//    }
//
//    public function parentInfo()
//    {
//        return $this->belongsTo('App\Models\Workspace', 'parent');
//    }

}
