<?php

namespace App\Controller;

use App\Model\Evenement;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AppController extends Controller
{
    public function home(Request $request, Response $response)
    {
        if (!$this->auth->check()) {
            return $this->redirect($response, 'login');
        }

        $evenements = Evenement::where('etat', '<>', Evenement::CREE)->orderBy('id', 'desc')->take(10)->get();

        return $this->view->render($response, 'App/home.twig', [
            'evenements' => $evenements
        ]);
    }

    public function search(Request $request, Response $response)
    {
        $query = $request->getParam('q');

        if (!$query) {
            return $this->view->render($response, 'Error/error.twig', [
                'title' => 'Recherche vide',
                'description' => 'Veuillez renseigner des mots clés pour lancer la recherche'
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