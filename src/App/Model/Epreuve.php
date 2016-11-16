<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Epreuve extends Model
{

  const CREE = 0;
  const VALIDE = 1;
  const OUVERT = 2;
  const EN_COURS = 3;
  const CLOS = 4;
  const EXPIRE = 5;
  const ANNULE = 6;


  protected $table = 'epreuve';

  protected $primaryKey = 'id';

  public $timestamps = false;

  protected $fillable = [
        'nom',
        'capacite',
        'date_debut',
        'date_fin',
        'etat',
        'description',
        'prix'
    ];

  public function evenement()
  {
    return $this->belongsTo('App\Model\Evenement');
  }



}

