<?php

$app->get('/home', 'AppController:home')->setName('home');
$app->get('/evenement/{id_evenement:[0-9]+}', 'EvenementController:show')->setName("evenement.show");
$app->map(['GET', 'POST'],'/epreuves/join/{id_evenement:[0-9]+}', 'EpreuveController:join')->setName("epreuve.join");
$app->map(['GET', 'POST'],'/epreuves/payment', 'EpreuveController:payment')->setName("epreuve.payment");

$app->get('/epreuve/add', 'EpreuveController:getAddEpreuve')->setName('epreuve.add');
$app->post('/epreuve/add', 'EpreuveController:postAddEpreuve');
