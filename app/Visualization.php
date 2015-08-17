<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visualization extends Model
{
    public $timestamps = false;

    public function ratings()
    {
        return $this->hasMany('App\Rating');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function visualVariables()
    {
        return $this->belongsToMany('App\VisualVariable')->withPivot('type');
    }
}
