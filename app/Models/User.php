<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'name_bn',
        'name_en',
        'name_bn'
    ];

    public function UserGroup()
    {
        return $this->belongsTo('App\Models\UserGroup');
    }

    public function Workspace()
    {
        return $this->belongsTo('App\Models\Workspace');
    }

}
