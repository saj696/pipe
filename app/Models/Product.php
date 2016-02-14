<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [
      'id'
    ];
    public $timestamps = false;
    public $table = 'products';

    public function productTypes()
    {
        return $this->belongsTo('App\Models\ProductType','product_type_id');
    }
    public function materials()
    {
        return $this->belongsTo('App\Models\Material','color');
    }
}
