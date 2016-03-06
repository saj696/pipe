<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDeliveryDetail extends Model
{
    public $timestamps = false;
    protected $table = 'sales_delivery_details';
    protected $fillable = [
        'delivered_quantity',
        'last_delivered_quantity'
    ];
}
