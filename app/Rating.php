<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function visualization()
    {
        return $this->belongsTo('App\Visualization');
    }
}
