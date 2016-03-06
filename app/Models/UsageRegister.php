<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageRegister extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date',
        'material',
        'usage'
    ];

    public function Material()
    {
        return $this->belongsTo('App\Models\Material');
    }

    public function getDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = strtotime($value);
    }

}
