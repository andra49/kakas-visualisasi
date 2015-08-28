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
        return $this->belongsToMany('App\User')->withPivot(['count', 'rating', 'knowledge']);
    }

    public function systems()
    {
        return $this->belongsToMany('App\System')->withPivot(['rating']);
    }

    public function activities()
    {
        return $this->belongsToMany('App\Activity');
    }

    public function visualVariables()
    {
        return $this->belongsToMany('App\VisualVariable')->withPivot('type');
    }
}
