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

    public function sportifs()
    {
        return $this->belongsToMany('App\Model\Sportif', 'participe');
    }

    public function evenement()
    {
        return $this->belongsTo('App\Model\Evenement');
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
        $webPath = 'uploads/evenements/' . $this->evenement_id . '/epreuves/' . $this->id;
        $rootPath =  __DIR__ . '/../../../public/' . $webPath;
        return file_exists($rootPath . '.jpg') ? $webPath . '.jpg' : $webPath . '.png';
    }
}
