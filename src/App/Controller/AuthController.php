<?php

namespace App\Controller;

use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Organisateur;
use App\Model\Sportif;
use Respect\Validation\Validator as V;

class AuthController extends Controller
{
    public function login(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $credentials = [
                'email' => $request->getParam('email'),
                'password' => $request->getParam('password')
            ];
            $remember = $request->getParam('remember') ? true : false;

            try {
                if ($this->auth->authenticate($credentials, $remember)) {
                    $this->flash('success', 'Vous êtes maintenant connecté.');
                    return $this->redirect($response, 'user.compte');
                } else {
                    $this->flash('danger', 'Mauvaise adresse email ou mot de passe.');
                }
            } catch (ThrottlingException $e) {
                $this->flash('danger', 'Trop de connexions !');
            }

            return $this->redirect($response, 'login');
        }

        return $this->view->render($response, 'Auth/login.twig');
    }

    public function register(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $email = $request->getParam('email');
            $password = $request->getParam('password');
            $nom = $request->getParam('nom');
            $prenom = $request->getParam('prenom');
            $type = $request->getParam('type');

            $this->validator->validate($request, [
                'nom' => V::length(1, 50),
                'prenom' => V::length(1, 50),
                'email' => V::noWhitespace()->email(),
                'password' => V::noWhitespace()->length(6, 25),
                'password-confirm' => V::equals($password)
            ]);

            if ($this->auth->findByCredentials(['login' => $email])) {
                $this->validator->addError('email', 'Cette adresse email est déjà utilisée.');
            }

            if ($this->validator->isValid()) {
                $role = $this->auth->findRoleByName('User');
                $user = $this->auth->registerAndActivate([
                    'email' => $email,
                    'password' => $password,
                    'permissions' => [
                        'user.delete' => 0
                    ]
                ]);

                if ($type == 'organisateur') {
                    $organisateur = new Organisateur([
                        'nom' => $nom,
                        'prenom' => $prenom
                    ]);

                    $user->organisateur()->save($organisateur);
                } elseif ($type == 'sportif') {
                    $sportif = new Sportif([
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'email'=> $email
                    ]);

                    $user->sportif()->save($sportif);
                }

                $role->users()->attach($user);

                $this->flash('success', 'Votre compte a été créé.');
                return $this->redirect($response, 'login');
            }
        }

        return $this->view->render($response, 'Auth/register.twig');
    }

    public function logout(Request $request, Response $response)
    {
        $this->auth->logout();

        $this->flash('success', 'Vous avez été déconnecté.');
        return $this->redirect($response, 'login');
    }
}
