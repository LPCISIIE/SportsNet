<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as V;
use App\Model\Evenement;

class EvenementController extends Controller
{
    public function edit(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['id']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        if ($request->isPost()) {
            $this->validator->validate($request, [
                'nom' => V::length(1, 100),
                'adresse' => V::length(1, 100),
                'date_debut' => V::date('d/m/Y'),
                'date_fin' => V::date('d/m/Y'),
                'telephone' => V::phone(),
                'discipline' => V::length(1, 50)
            ]);

            if ($this->validator->isValid()) {
                $evenement->fill([
                    'nom' => $request->getParam('nom'),
                    'adresse' => $request->getParam('adresse'),
                    'date_debut' => $request->getParam('date_debut'),
                    'date_fin' => $request->getParam('date_fin'),
                    'telephone' => $request->getParam('telephone'),
                    'discipline' => $request->getParam('discipline')
                ]);

                $evenement->save();

                $this->flash('success', 'L\'événement "' . $evenement->nom . '" a bien été modifié !');
                return $this->redirect($response, 'home');
            }
        }

        return $this->view->render($response, 'Evenement/edit.twig', [
            'evenement' => $evenement
        ]);
    }
}