<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Organisateur extends Model
{
    protected $table = 'organisateur';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'paypal',
    ];
}
