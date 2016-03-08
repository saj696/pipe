<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    public $timestamps = false;
    protected $table = 'sales_return';


    public function workspaces()
    {
        return $this->belongsTo('App\Models\Workspace', 'workspace_id');
    }


}
