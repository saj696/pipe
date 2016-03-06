<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroupRole extends Model
{
    public $timestamps = false;

    protected $fillable = [

    ];

    public function component()
    {
        return $this->belongsTo('App\Models\Component');
    }

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }

    public function task()
    {
        return $this->belongsTo('App\Models\Task');
    }

    public function userGroup()
    {
        return $this->belongsTo('App\Models\UserGroup');
    }

}
