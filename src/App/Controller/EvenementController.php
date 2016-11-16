<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Evenement as Evenement;
use App\Model\Epreuve as Epreuve;
use Respect\Validation\Validator as V;

class EvenementController extends Controller
{
    public function show(Request $request, Response $response, $args){
        $id_evenement = $args["id_evenement"];
        $evenement = Evenement::find($id_evenement);
        $epreuves = $evenement->epreuves()->get()->toArray();
        $evenement = $evenement->toArray();
        return $this->view->render($response, 'Evenement/show.twig', compact('evenement', 'epreuves'));
    }
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
                'discipline' => V::length(1, 50),
                'description' => V::notBlank()
            ]);

            if ($this->validator->isValid()) {
                $evenement->fill([
                    'nom' => $request->getParam('nom'),
                    'adresse' => $request->getParam('adresse'),
                    'date_debut' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_debut')),
                    'date_fin' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_fin')),
                    'telephone' => $request->getParam('telephone'),
                    'discipline' => $request->getParam('discipline'),
                    'description' => $request->getParam('description')
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
