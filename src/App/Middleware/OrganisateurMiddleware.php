<?php

namespace App\Middleware;

class OrganisateurMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $organisateur = $this->auth->getUser()->organisateur;

        if (!$organisateur) {
            return $this->view->render($response, 'Error/error.twig', [
                'title' => 'Droits insuffisants',
                'description' => 'Vous devez être connecté en tant qu\'organisateur pour accéder à cette page !'
            ]);
        }

        return $next($request, $response);
    }
}
