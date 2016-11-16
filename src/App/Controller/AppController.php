<?php

namespace App\Controller;

use App\Model\Epreuve;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AppController extends Controller
{
    public function home(Request $request, Response $response)
    {
        return $this->view->render($response, 'App/home.twig');
    }

    public function show(Request $request, Response $response, array $args)
    {
        $epreuve = Epreuve::find($args['id']);

        if (!$epreuve) {
            throw $this->notFoundException($request, $response);
        }

        return $this->view->render($response, 'Epreuve/show.twig', [
            'epreuve' => $epreuve
        ]);
    }
}