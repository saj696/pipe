<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasesReturn extends Model
{
    public $guarded = ['id'];
    public $table = 'purchases_return';
    public $timestamps = false;

    public function getPurchaseReturnDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }
    public function setPurchaseReturnDateAttribute($value)
    {
        $this->attributes['purchase_return_date'] = strtotime($value);
    }
    public function supplier()
    {
        return  $this->belongsTo('App\Models\Supplier','supplier_id');
    }
    public function PurchasesReturnDetail()
    {
        return  $this->hasMany('App\Models\PurchasesReturnDetail');
    }
}
