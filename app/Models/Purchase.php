<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $guarded =['id'];
    public $timestamps = false;

    public function getPurchaseDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }
    public function setPurchaseDateAttribute($value)
    {
        $this->attributes['purchase_date']=strtotime($value);
    }
    public function supplier()
    {
        return  $this->belongsTo('App\Models\Supplier','supplier_id');
    }
    public function purchaseDetails()
    {
        return  $this->hasMany('App\Models\PurchaseDetail');
    }
}
