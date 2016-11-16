<?php

$app->group('', function () {
    $this->map(['GET', 'POST'], '/profil', 'UserController:monCompte')->setName('user.compte');
    $this->get('/mes-evenements', 'UserController:mesEvenements')->setName('user.events');
})->add(new App\Middleware\AuthMiddleware($container));
