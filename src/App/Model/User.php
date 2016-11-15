<?php

namespace App\Model;

use Cartalyst\Sentinel\Users\EloquentUser;
use App\Model\Organisateur as Organisateur;

class User extends EloquentUser
{
    protected $table = 'user';

    protected $primaryKey = 'id';

    protected $fillable = [
        'email',
        'password',
        'permissions',
    ];

    public function organisateur(){
      return $this->hasOne('App\Model\Organisateur');
    }
}
