<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public $timestamps=false;

    protected $fillable = [
        'product_id',
        'quantity'
    ];

    public function Workspace()
    {
        return $this->belongsTo('App\Models\Workspace');
    }

    public function Product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
