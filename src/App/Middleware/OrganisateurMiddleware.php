<?php

namespace App\Middleware;
use App\Model\Organisateur;

class OrganisateurMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $user =$this->auth->getUser();
        $organisateur = Organisateur::where('user_id', $user->id)->first();
        if (!$organisateur)
        return $this->view->render($response, 'error.twig',[
                'titreErreur' => "Droits insuffisants",
                'descriptionErreur' => "Vous devez être connecter en tant qu'organisateur pour accéder à cette page"
        ]);
        return $next($request, $response);
    }
}
