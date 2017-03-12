<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerShip extends Model
{
    protected $fillable = [
        'name','percent'
    ];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
