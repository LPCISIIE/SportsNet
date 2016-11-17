<?php

$app->get('/home', 'AppController:home')->setName('home');
$app->map(['GET', 'POST'],'/epreuves/join/{id_evenement:[0-9]+}', 'EpreuveController:join')->setName("epreuve.join");
$app->map(['GET', 'POST'],'/epreuves/payment', 'EpreuveController:payment')->setName("epreuve.payment");
