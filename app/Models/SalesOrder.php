<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $table = 'sales_order';
    public $timestamps = false;


    public function salesOrderItems()
    {
        return $this->hasMany('App\Models\SalesOrderItem');
    }

    public function workspaces()
    {
        return $this->belongsTo('App\Models\Workspace','workspace_id');
    }


}
