<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [
      'id'
    ];
    public $timestamps = false;

    public function product_types()
    {
        return $this->belongsTo('app/Models/ProductType');
        return $this->belongsTo('app/Models/Material');
    }
    public function materials()
    {
        return $this->belongsTo('app/Models/Material');
    }
}
