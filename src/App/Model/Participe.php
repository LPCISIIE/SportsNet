<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Participe extends Model
{
    protected $table = 'participe';

    public $timestamps = false;

    protected $fillable = [
        'numero_participant'
    ];
}