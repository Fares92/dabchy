<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    protected $fillable = [
        'name'
    ];
    public function sub_interests()
    {
        return $this->hasMany('App\Sub_interest');
    }
}
