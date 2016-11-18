<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Sportif extends Model
{
    protected $table = 'sportif';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'birthday'
    ];

    public function epreuves()
    {
        return $this->belongsToMany('App\Model\Epreuve', 'participe');
    }
}