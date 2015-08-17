<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataSelection extends Model
{
    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo('App\VisualizationProject');
    }
}
