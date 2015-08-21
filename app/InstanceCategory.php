<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstanceCategory extends Model
{
    public $timestamps = false;
    
    public function dataset()
    {
        return $this->belongsTo('App\InstanceCategory');
    }
}
