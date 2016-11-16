<?php
/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 15/11/2016
 * Time: 11:36
 */

namespace App\Controller;


use App\Model\Evenement;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class OrganisateurController
{
    public function creerEpreuve(Request $request, Response $response)
    {
        $nom = $request->getParam('nom');
        $dateDebut = $request->getParam('dateDebut');
        $dateFin = $request->getParam('dateFin');
        $adresse = $request->getParam('adresse');
        $telephone = $request->getParam('telephone');
        $discipline = $request->getParam('discipline');
        $description = $request->getParam('discipline');
        $etat = $request->getParam('etat');

        Evenement::create(
            [   'nom' => $nom,
                'date_debut' => $dateDebut ,
                'date_fin' => $dateFin,
                'adresse' => $adresse ,
                'telephone' => $telephone,
                'discipline' => $discipline,
                'description' => $description,
                'etat' => $etat  ]
        );
    }
}