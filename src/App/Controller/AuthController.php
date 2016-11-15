<?php

namespace App\Controller;

use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Organisateur as Organisateur;
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
                    $this->flash('success', 'You have been logged in.');
                    return $this->redirect($response, 'home');
                } else {
                    $this->flash('danger', 'Bad username or password.');
                }
            } catch (ThrottlingException $e) {
                $this->flash('danger', 'Too many attempts!');
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

            $this->validator->validate($request, [
                'nom' => V::noWhitespace(),
                'prenom' => V::noWhitespace(),
                'email' => V::noWhitespace()->email(),
                'password' => V::noWhitespace()->length(6, 25),
                'password-confirm' => V::equals($password)
            ]);

            if ($this->auth->findByCredentials(['login' => $email])) {
                $this->validator->addError('email', 'User already exists with this email address.');
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
                $organisateur = new Organisateur([
                    'nom' => $nom,
                    'prenom' => $prenom
                ]);
                $user->organisateur()->save($organisateur);
                $role->users()->attach($user);

                $this->flash('success', 'Your account has been created.');
                return $this->redirect($response, 'login');
            }
        }

        return $this->view->render($response, 'Auth/register.twig');
    }

    public function logout(Request $request, Response $response)
    {
        $this->auth->logout();

        $this->flash('success', 'You have been logged out.');
        return $this->redirect($response, 'home');
    }
}
