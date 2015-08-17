<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'password'];
    protected $hidden = ['password'];
    public $timestamps = false;

    public function ratings()
    {
        return $this->hasMany('App\Rating');
    }

    public function projects()
    {
        return $this->hasMany('App\VisualizationProject');
    }

    public function visualizations()
    {
        return $this->belongsToMany('App\Visualization');
    }
}
