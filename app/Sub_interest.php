<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sub_interest extends Model
{
    protected $fillable = [
        'id','name','name_ar'
    ];

    public function services()
    {
        return $this->hasMany('App\Service');
    }
    public function interest()
    {
        return $this->belongsTo('App\Interest');
    }
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
