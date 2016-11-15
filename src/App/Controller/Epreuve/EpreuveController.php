<?php

namespace App\Controller\Epreuve;

use App\Controller\Controller;
use Respect\Validation\Validator as v;
use App\Model\Epreuve;

class EpreuveController extends Controller
{
    public function getAddEpreuve($request, $response) {
        return $this->view->render($response, 'Epreuve/add.twig');
    }

    public function postAddEpreuve($request, $response) {

        //validateur
       v::with('App\\Validation\\Rules\\');
        $validation = $this->validator->validate($request, [
            // à valider avec les paramètres de $request
            'epreuve_name' => v::notEmpty(),
            'date_debut' => v::date('d-m-Y'),
            'heure_debut' => v::date('H:i'),
            'date_fin' => v::date('d-m-Y'),
            'heure_fin' => v::date('H:i'),
            'epreuve_pic_link' => v::ImageFormat()->ImageSize(),
            'epreuve_description' => v::notEmpty(),
            'capacite' => v::notEmpty()->numeric(),
            'prix' => v::notEmpty()->numeric(),
            'op' => v::equals('reg'),
        ]);

        /**
        * If the fields fail, then redirect back to signup
        */
        if (!($validation->isValid())) {
            return $this->getAddEpreuve($request,$response);
        }
        $dated = \DateTime::createFromFormat("d-m-Y H:i",$request->getParam('date_debut')." ".$request->getParam('heure_debut'));
        $datef = \DateTime::createFromFormat("d-m-Y H:i",$request->getParam('date_fin')." ".$request->getParam('heure_fin'));
        $epreuve = new Epreuve();
        $epreuve->epreuve_name=$request->getParam('epreuve_name');
        $epreuve->epreuve_date_debut=$dated;
        $epreuve->epreuve_date_fin=$datef;
        $epreuve->epreuve_description=$request->getParam('epreuve_description');
        $epreuve->epreuve_capacite=$request->getParam('capacite');
        $epreuve->epreuve_prix=$request->getParam('prix');
        //$epreuve->save();

        echo "<pre>";
        print_r($dated);
        exit();

        return $this->view->render($response, 'Organisateur/dashboard.twig');
    }


}