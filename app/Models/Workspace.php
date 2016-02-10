<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'name',
        'type',
        'location',
        'parent'
    ];

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function parentInfo()
    {
        return $this->belongsTo('App\Models\Workspace', 'parent');
    }

}
