<?php

namespace App\Controller;

use App\Model\User;
use App\Model\Sportif;
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
                'birthday' => V::optional(V::date('d/m/Y')),
                'email' => V::email()
            ]);

            if ($this->validator->isValid()) {
                $user->email = $request->getParam('email');
                $user->save();

                if ($organisateur) {
                    $organisateur->fill([
                        'nom' => $request->getParam('nom'),
                        'prenom' => $request->getParam('prenom'),
                        'paypal' => $request->getParam('paypal')
                    ]);
                    $organisateur->save();
                } else {
                    $sportif = $user->sportif;
                    $sportif->fill([
                        'nom' => $request->getParam('nom'),
                        'prenom' => $request->getParam('prenom'),
                        'birthday' => $request->getParam('birthday') ? \DateTime::createFromFormat('d/m/Y', $request->getParam('birthday')) : null
                    ]);
                    $sportif->save();
                }

                $this->flash('success', 'Votre compte a bien été modifié !');
                return $this->redirect($response, 'user.compte');
            }
        }

        return $this->view->render($response, 'User/mon-compte.twig', [
            'organisateur' => $organisateur,
            'sportif' => $organisateur ? null : $user->sportif
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
            'evenements' => $this->user()->evenements()->orderBy('id', 'desc')->get()
        ]);
    }

    public function mesEpreuves(Request $request, Response $response)
    {
        $sportif = Sportif::where('user_id', $this->user()->id)->first();
        $epreuves = null;
        if ($sportif) {
            $epreuves = $sportif->epreuves;
        }

        return $this->view->render($response, 'User/mes-epreuves.twig', [
            'epreuves' => $epreuves
        ]);
    }
}
