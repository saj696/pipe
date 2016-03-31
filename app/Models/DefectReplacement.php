<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefectReplacement extends Model
{
    public $timestamps = false;
    protected $table = 'defect_replacements';

    public function defect()
    {
        return $this->belongsTo('App\Models\Defect');
    }

    public function defectReplacementItems()
    {
        return $this->hasMany('App\Models\DefectReplacementItem', 'defect_id');
    }
}
