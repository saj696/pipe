<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    public $timestamps = false;

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

    public function workspaceLedger()
    {
        return $this->hasMany('App\Models\WorkspaceLedger', 'workspace_id');
    }

}
