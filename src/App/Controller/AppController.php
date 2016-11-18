<?php

namespace App\Controller;

use App\Model\Evenement;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AppController extends Controller
{
    public function home(Request $request, Response $response)
    {
        return $this->view->render($response, 'App/home.twig');
    }

    public function search(Request $request, Response $response)
    {
        $query = $request->getParam('q');

        if (!$query) {
            return $this->view->render($response, 'error.twig', [
                'titreErreur' => 'Recherche vide',
                'descriptionErreur' => 'Veuillez renseigner des mots clés pour lancer la recherche'
            ]);
        }

        $evenements = Evenement::where('nom', 'like', '%' . $query . '%')
            ->orWhere('adresse', 'like', '%' . $query . '%')
            ->orWhere('discipline', 'like', '%' . $query . '%')
            ->get();

        return $this->view->render($response, 'App/search.twig', [
            'evenements' => $evenements,
            'query' => $query
        ]);
    }
}