<?php

$app->group('', function () {
    $this->get('/evenement/{id_evenement:[0-9]+}/epreuve/add', 'EpreuveController:getAddEpreuve')->setName('epreuve.add');
    $this->post('/evenement/{id_evenement:[0-9]+}/epreuve/add', 'EpreuveController:postAddEpreuve');

    $this->map(['GET','POST'],'/evenement/{id_evenement:[0-9]+}/{id_epreuve:[0-9]+}', 'EpreuveController:edit')
    ->setName('trial.edit');

})->add(new App\Middleware\AuthMiddleware($container));
