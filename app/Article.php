<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'name','date','nb_jaime','categorie','brund','description','prix_achat','prix_vente','remise','city',
        'taille','user_id','couleur','etat','image','nb_comment'
        
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function jusers()
    {
        return $this->belongsToMany('App\User');
    }
}
