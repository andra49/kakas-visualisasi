<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public $timestamps = false;

    public function visualizations()
    {
        return $this->belongsToMany('App\Visualization');
    }
}
