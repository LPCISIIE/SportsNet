<?php

namespace App\Controller;

use App\Model\User;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as V;

class UserController extends Controller
{
    public function monCompte(Request $request, Response $response)
    {
        $user = $this->user();
        $is_organisateur = $this->isOrganisateur();
        if ($is_organisateur){
            $profil = $user->organisateur;
        else{
            $profil = $user->sportif;
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

                $profil->fill([
                    'nom' => $request->getParam('nom'),
                    'prenom' => $request->getParam('prenom'),
                    'paypal' => $request->getParam('paypal')
                ]);
                $profil->save();

                $this->flash('success', 'Votre compte a bien été modifié !');
                return $this->redirect($response, 'user.compte');
            }
        }

        return $this->view->render($response, 'User/mon-compte.twig', [
            'organisateur' => $profil,
            'is_organisateur' => $is_organisateur
        ]);
    }

    public function profil(Request $request, Response $response, array $args)
    {
        $user = User::find($args['id']);

        if (!$user) {
            throw $this->notFoundException($request, $response);
        }

        return $this->view->render($response, 'User/profil.twig', [
            'user' => $user,
            'organisateur' => $user->organisateur
        ]);
    }

    public function mesEvenements(Request $request, Response $response)
    {
        return $this->view->render($response, 'User/mes-evenements.twig', [
            'evenements' => $this->user()->evenements
        ]);
    }
}
