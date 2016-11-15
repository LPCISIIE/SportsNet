<?php

$app->group('', function () {
    $this->get('/mes-evenements', 'UserController:mesEvenements')->setName('user.events');
})->add(new App\Middleware\AuthMiddleware($container));
