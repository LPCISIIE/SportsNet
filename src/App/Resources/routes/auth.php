<?php

$app->group('', function () {
    $this->map(['GET', 'POST'], '/login', 'AuthController:login')->setName('login');
    $this->map(['GET', 'POST'], '/', 'AuthController:register')->setName('register');
})->add(new App\Middleware\GuestMiddleware($container));

$app->group('', function () {
    $this->get('/logout', 'AuthController:logout')->setName('logout');

    $this->get('/evenement/{id_evenement:[0-9]+}/epreuve/add', 'EpreuveController:getAddEpreuve')->setName('epreuve.add');
	$this->post('/evenement/{id_evenement:[0-9]+}/epreuve/add', 'EpreuveController:postAddEpreuve');
})->add(new App\Middleware\AuthMiddleware($container));
