<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    protected $fillable = ['username', 'password'];
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
        return $this->belongsToMany('App\Visualization')->withPivot(['count', 'rating']);
    }
}
