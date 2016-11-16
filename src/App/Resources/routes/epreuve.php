<?php

$app->group('', function () {
    $this->get('/evenement/{id_evenement:[0-9]+}/epreuve/add', 'EpreuveController:getAddEpreuve')->setName('epreuve.add');
    $this->post('/evenement/{id_evenement:[0-9]+}/epreuve/add', 'EpreuveController:postAddEpreuve');
})->add(new App\Middleware\AuthMiddleware($container));
