<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as V;

class UserController extends Controller
{
    public function monCompte(Request $request, Response $response)
    {
        $user = $this->user();
        $organisateur = $user->organisateur;

        if ($request->isPost()) {
            $this->validator->validate($request, [
                'nom' => V::length(1, 50),
                'prenom' => V::length(1, 50),
                'paypal' => V::optional(V::email()),
                'email' => V::email()
            ]);

            if ($this->validator->isValid()) {
                $user->email = $request->getParam('email');
                $user->save();

                $organisateur->fill([
                    'nom' => $request->getParam('nom'),
                    'prenom' => $request->getParam('prenom'),
                    'paypal' => $request->getParam('paypal')
                ]);
                $organisateur->save();

                $this->flash('success', 'Votre compte a bien été modifié !');
                return $this->redirect($response, 'user.compte');
            }
        }

        return $this->view->render($response, 'User/mon-compte.twig', [
            'organisateur' => $organisateur
        ]);
    }

    public function mesEvenements(Request $request, Response $response)
    {
        return $this->view->render($response, 'User/mes-evenements.twig', [
            'evenements' => $this->user()->evenements
        ]);
    }
}