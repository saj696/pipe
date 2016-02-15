<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $guarded =['id'];
    public $timestamps = false;
    public $table = 'purchases_details';
    public function Purchase(){
        return $this->belongsTo('App\Models\Purchase','purchase_id');
    }
    public function Material(){
        return $this->belongsTo('App\Models\Material','material_id');
    }
}
