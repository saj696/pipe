<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    public $timestamps = false;
    protected $table = 'sales_order';

    public function salesOrderItems()
    {
        return $this->hasMany('App\Models\SalesOrderItem');
    }

    public function workspaces()
    {
        return $this->belongsTo('App\Models\Workspace', 'workspace_id');
    }

    public function getDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = strtotime($value);
    }
}
