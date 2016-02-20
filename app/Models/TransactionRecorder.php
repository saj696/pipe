<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TransactionRecorder extends Model
{
    public $timestamps=false;

    protected $fillable = [];

    public function getDateAttribute($value)
    {
        return date('d-m-Y', $value);
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date']=strtotime($value);
    }

}
