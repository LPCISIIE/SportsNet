<?php

namespace App\Middleware;

use App\Model\Organisateur;

class OrganisateurMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $user = $this->auth->getUser();
        $organisateur = Organisateur::where('user_id', $user->id)->first();

        if (!$organisateur) {
            return $this->view->render($response, 'Error/error.twig', [
                'title' => 'Droits insuffisants',
                'description' => 'Vous devez être connecté en tant qu\'organisateur pour accéder à cette page !'
            ]);
        }

        return $next($request, $response);
    }
}
