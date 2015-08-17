<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisualizationProject extends Model
{
    public $timestamps = false;

    public function dataSelections()
    {
        return $this->hasMany('App\DataSelection');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function dataset()
    {
        return $this->belongsTo('App\Dataset');
    }
}
