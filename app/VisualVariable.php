<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisualVariable extends Model
{
    protected $table = 'visual_variables';
    public $timestamps = false;

    public function visualizations()
    {
        return $this->belongsToMany('App\Visualization')->withPivot('type');
    }
}
