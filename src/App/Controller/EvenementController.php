<?php

namespace App\Controller;

use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
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

}
