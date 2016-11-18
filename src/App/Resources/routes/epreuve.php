<?php

$app->get('/evenement/{event_id:[0-9]+}/epreuve/{trial_id:[0-9]+}', 'EpreuveController:show')->setName('epreuve.show');
$app->map(['GET', 'POST'], '/evenement/{event_id:[0-9]+}/epreuve/{trial_id:[0-9]+}/searchMe', 'EpreuveController:resultatPerso')->setName('recherchePerso');
$app->map(['GET', 'POST'], '/evenement/{event_id:[0-9]+}/epreuve/{trial_id:[0-9]+}/result', 'EpreuveController:resultat')->setName('resultat');
$app->map(['GET', 'POST'], '/evenement/{id_evenement:[0-9]+}/join', 'EpreuveController:join')->setName('epreuve.join');
$app->map(['GET', 'POST'], '/epreuves/payment', 'EpreuveController:payment')->setName('epreuve.payment');

$app->group('', function () {
    $this->map(['GET', 'POST'], '/evenement/{event_id:[0-9]+}/epreuve/add', 'EpreuveController:add')->setName('epreuve.add');
    $this->map(['GET', 'POST'], '/evenement/{event_id:[0-9]+}/epreuve/{trial_id:[0-9]+}/edit', 'EpreuveController:edit')->setName('trial.edit');
})->add(new App\Middleware\AuthMiddleware($container));
