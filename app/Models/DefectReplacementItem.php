<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefectReplacementItem extends Model
{
    public $timestamps = false;
    protected $table = 'defect_replacement_items';

    public function defectReplacement()
    {
        return $this->belongsTo('App\Models\DefectReplacement');
    }

}