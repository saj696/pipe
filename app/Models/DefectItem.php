<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefectItem extends Model
{
    public $timestamps = false;
    protected $table = 'defect_items';

    public function defect()
    {
        return $this->belongsTo('App\Models\Defect');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}
