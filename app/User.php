<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role','phone','photo','provider_user_id', 'provider'
        ,'location',


    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function articles()
    {
        return $this->hasMany('App\Article');
    }
    public function favoris()
    {
        return $this->hasMany('App\favorit');
    }
    public function jarticles()
    {
        return $this->belongsToMany('App\Article');
    }




//    public function sub_interests()
//    {
//        return $this->belongsToMany('App\Sub_interest');
//    }
//    public function services()
//    {
//        return $this->hasMany('App\Service');
//    }
//    public function business_photos()
//    {
//        return $this->hasMany('App\Business_photo');
//    }
//    public function menu_photos()
//    {
//        return $this->hasMany('App\Menu_photo');
//    }
//    public function advertisements()
//    {
//        return $this->hasMany('App\Advertisement');
//    }
//    public function facilities()
//    {
//        return $this->hasMany('App\Facility');
//    }
//    public function days()
//    {
//        return $this->belongsToMany('App\Day')->withPivot('h_from', 'h_to');
//    }
//    public function partnerShips()
//    {
//        return $this->hasMany('App\PartnerShip');
//    }
//    public function following()
//    {
//        return $this->belongsToMany('App\User', 'likes', 'salon_id', 'user_id');
//    }
//    public function followers()
//    {
//        return $this->belongsToMany('App\User', 'likes', 'user_id', 'salon_id');
//    }
}
