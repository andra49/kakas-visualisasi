<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    public $timestamps = false;

    public function visualizations()
    {
        return $this->belongsToMany('App\Visualization')->withPivot(['rating']);
    }
}
