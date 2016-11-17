<?php

namespace App\Model;

use Cartalyst\Sentinel\Users\EloquentUser;

class User extends EloquentUser
{
    protected $table = 'user';

    protected $primaryKey = 'id';

    protected $fillable = [
        'email',
        'password',
        'permissions',
    ];

    public function organisateur()
    {
      return $this->hasOne('App\Model\Organisateur');
    }

    public function sportif()
    {
      return $this->hasOne('App\Model\Sportif');
    }

    public function evenements()
    {
        return $this->hasMany('App\Model\Evenement');
    }
    public function checkOrganisateur()
    {
        return (Organisateur::where('user_id', $this->id)->first()) ? true : false;
    }
}
