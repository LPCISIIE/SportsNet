<?php
/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 15/11/2016
 * Time: 10:24
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
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
        'etat',
    ];

    public function cloturer()
    {
        $this->etat = 0;
    }

    public function ouvrir()
    {
        $this->etat = 1;
    }
}