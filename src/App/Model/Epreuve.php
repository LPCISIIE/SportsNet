<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Epreuve extends Model{
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

      public function sportifs(){
         return $this->belongsToMany('App\Model\Sportif', "participe");
      }
}
