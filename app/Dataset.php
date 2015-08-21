<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    public function projects()
    {
        return $this->hasMany('App\VisualizationProject');
    }

    public function attributes()
    {
        return $this->hasMany('App\Attribute');
    }

    public function categories()
    {
        return $this->hasMany('App\InstanceCategory');
    }
}
