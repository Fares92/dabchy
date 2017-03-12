<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name','name_ar','price','description','type_price'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function sub_interest()
    {
        return $this->belongsTo('App\Sub_interest');
    }
}
