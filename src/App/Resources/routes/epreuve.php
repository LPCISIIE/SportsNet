<?php

$app->get('/evenement/{event_id:[0-9]+}/epreuve/{trial_id:[0-9]+}', 'EpreuveController:show')->setName('epreuve.show');
$app->map(['GET', 'POST'], '/epreuves/join/{id_evenement:[0-9]+}', 'EpreuveController:join')->setName('epreuve.join');
$app->map(['GET', 'POST'], '/epreuves/payment', 'EpreuveController:payment')->setName('epreuve.payment');

$app->group('', function () {
    $this->map(['GET', 'POST'], '/evenement/{event_id:[0-9]+}/epreuve/add', 'EpreuveController:add')->setName('epreuve.add');
    $this->map(['GET', 'POST'], '/evenement/{event_id:[0-9]+}/epreuve/{trial_id:[0-9]+}/edit', 'EpreuveController:edit')->setName('trial.edit');
})->add(new App\Middleware\AuthMiddleware($container));

$app->map(['GET', 'POST'], '/{event_id:[0-9]+}/{trial_id:[0-9]+}/searchMe', 'EpreuveController:resultatPerso')->setName('recherchePerso');
