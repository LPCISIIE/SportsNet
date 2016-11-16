<?php

$app->get('/home', 'AppController:home')->setName('home');
$app->get('/evenement/{id_evenement:[0-9]+}', 'EvenementController:show')->setName("evenement.show");

$app->get('/epreuve/add', 'EpreuveController:getAddEpreuve')->setName('epreuve.add');
$app->post('/epreuve/add', 'EpreuveController:postAddEpreuve');
