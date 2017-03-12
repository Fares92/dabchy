<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $fillable = [
        'name'
    ];
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
