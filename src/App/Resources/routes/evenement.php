<?php

$app->get('/evenement/{id_evenement:[0-9]+}', 'EvenementController:show')->setName('evenement.show');

$app->group('', function () {
    $this->map(['GET', 'POST'], '/evenement/add', 'EvenementController:add')->setName('evenement.create');
    $this->map(['GET', 'POST'], '/evenement/{id:[0-9]+}/edit', 'EvenementController:edit')->setName('evenement.edit');

    $this->get('/evenement/{id:[0-9]+}/cancel', 'EvenementController:cancel')->setName('evenement.cancel');
    $this->get('/evenement/{id:[0-9]+}/delete', 'EvenementController:delete')->setName('evenement.delete');
    $this->get('/evenement/{id:[0-9]+}/getlist', 'EvenementController:getParticipants')->setName('evenement.participants');
})->add(new App\Middleware\AuthMiddleware($container));
