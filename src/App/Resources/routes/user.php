<?php

$app->get('/user/{id:[0-9]+}', 'UserController:profil')->setName('user.profile');

$app->group('', function () {
    $this->map(['GET', 'POST'], '/profil', 'UserController:monCompte')->setName('user.compte');
})->add(new App\Middleware\AuthMiddleware($container));

$app->group('', function () {
  $this->get('/mes-evenements', 'UserController:mesEvenements')->setName('user.events');
})->add(new App\Middleware\OrganisateurMiddleware($container));
