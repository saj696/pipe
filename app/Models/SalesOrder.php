<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $table = 'sales_order';
    public $timestamps = false;

    protected $fillable = [
        'customer_type',
    ];
}
