<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasesReturnDetail extends Model
{
    public $guarded = ['id'];
    public $table = 'purchases_return_details';
    public $timestamps = false;
}
