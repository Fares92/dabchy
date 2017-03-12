<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business_photo extends Model
{
    protected $fillable = [
        'name'
    ];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
