<?php

$app->get('/evenement/{id_evenement:[0-9]+}', 'EvenementController:show')->setName('evenement.show');

$app->group('', function () {
    $this->map(['GET', 'POST'], '/evenement/create', 'EvenementController:create')->setName('evenement.create');
    $this->map(['GET', 'POST'], '/evenement/{id:[0-9]+}/edit', 'EvenementController:edit')->setName('evenement.edit');
    $this->get('/evenement/{id:[0-9]+}/cancel', 'EvenementController:annuler')->setName('evenement.cancel');
})->add(new App\Middleware\AuthMiddleware($container));
