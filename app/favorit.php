<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class favorit extends Model
{
    protected $fillable = [
        'favorite_color','favorite_brand','user_id','saison'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
