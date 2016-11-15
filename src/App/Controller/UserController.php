<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserController extends Controller
{
    public function mesEvenements(Request $request, Response $response)
    {
        return $this->view->render($response, 'User/mes-evenements.twig', [
            'evenements' => $this->user()->evenements
        ]);
    }
}