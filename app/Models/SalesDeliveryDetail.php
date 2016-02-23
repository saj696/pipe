<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDeliveryDetail extends Model
{
    protected $table = 'sales_delivery_details';
    public $timestamps = false;

    protected $fillable= [
        'delivered_quantity',
        'last_delivered_quantity'
    ];
}
