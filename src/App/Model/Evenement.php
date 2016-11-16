<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    const CREE = 0;
    const VALIDE = 1;
    const OUVERT = 2;
    const EN_COURS = 3;
    const CLOS = 4;
    const EXPIRE = 5;
    const ANNULE = 6;

    protected $table = 'evenement';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nom',
        'date_debut',
        'date_fin',
        'adresse',
        'telephone',
        'discipline',
        'description',
        'etat'
    ];

    public function epreuves()
    {
        return $this->hasMany('App\Model\Epreuve');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }


}
