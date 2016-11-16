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

    public function getStateFromValue($state)
    {
        switch ($state) {
            case self::CREE:
                return 'Créé';
            case self::VALIDE:
                return 'Validé';
            case self::OUVERT:
                return 'Ouvert à l\'inscription';
            case self::EN_COURS:
                return 'Validé';
            case self::CLOS:
                return 'Clos à l\'inscription';
            case self::EXPIRE:
                return 'Expiré';
            case self::ANNULE:
                return 'Annulé';

            default:
                return '';
        }
    }

    public function getState()
    {
        return $this->getStateFromValue($this->etat);
    }

    public function getWebPath()
    {
        $webPath = 'uploads/evenements/' . $this->id . '/header';
        $rootPath =  __DIR__ . '/../../../public/' . $webPath;
        return file_exists($rootPath . '.jpg') ? $webPath . '.jpg' : $webPath . '.png';
    }
}
