<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawStock extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'material_id',
        'quantity'
    ];

    public function material()
    {
        return $this->belongsTo('App\Models\Material');
    }
}
