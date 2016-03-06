<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    public $timestamps = false;
    public $table = 'purchases_details';
    protected $guarded = ['id'];

    public function Purchase()
    {
        return $this->belongsTo('App\Models\Purchase', 'purchase_id');
    }

    public function Material()
    {
        return $this->belongsTo('App\Models\Material', 'material_id');
    }
}
