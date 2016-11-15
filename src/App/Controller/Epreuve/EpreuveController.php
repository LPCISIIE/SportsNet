<?php

namespace App\Controller;

use Respect\Validation\Validator as V;
use App\Model\Epreuve as Epreuve;

class AuthController extends Controller
{
    public function getAddEpreuve($request, $response, $args) {
        return $this->view->render($response, 'Epreuve/add.twig');
    }

    public function postAddEpreuve($request, $response) {

        //validateur
        v::with('App\\Validation\\Rules\\');
        $validation = $this->validator->validate($request, [
            // à valider avec les paramètres de $request
           'op' => v::equals('reg'),
        ]);

        /**
        * If the fields fail, then redirect back to signup
        */
        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor(''));
        }

        $epreuve = new Epreuve();
        $epreuve->epreuve_name=$request->getParam('epreuve_name');
        $epreuve->epreuve_date_debut=$request->getParam('date_debut');
        $epreuve->epreuve_date_fin=$request->getParam('date_fin');
        $epreuve->epreuve_capacite=$request->getParam('capacite');
        $epreuve->epreuve_prix=$request->getParam('prix');
        $epreuve->save();

        return $this->view->render($response, 'Organisateur/dashboard.twig');
    }


}