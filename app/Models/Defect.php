<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    public $timestamps = false;
    protected $table = 'defects';

    public function defectItems()
    {
        return $this->hasMany('App\Models\DefectItem');
    }

    public function workspaces()
    {
        return $this->belongsTo('App\Models\Workspace', 'workspace_id');
    }
}
