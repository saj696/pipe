<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $table = 'sales_order_items';
    public $timestamps = false;


    public function salesOrder()
    {
        return $this->belongsTo('App\Models\SalesOrder','sales_order_id');
    }

}
